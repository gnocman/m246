<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Service;

use NamCong\ReturnShield\Model\Config;
use NamCong\ReturnShield\Model\RiskAnalysis;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\DataObject;
use Magento\Review\Model\Review;
use Magento\Review\Model\AppendSummaryDataFactory;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RiskAnalyzer
{
    public function __construct(
        private readonly Config $config,
        private readonly AppendSummaryDataFactory $appendSummaryDataFactory,
        private readonly ReviewCollectionFactory $reviewCollectionFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function analyze(ProductInterface $product, ?int $customerGroupId = null): RiskAnalysis
    {
        $storeId = (int)($product->getStoreId() ?: $this->storeManager->getStore()->getId());
        $score = max(0, (int)$product->getData('return_risk_manual_adjustment'));
        $reasons = [];
        $recommendations = [];
        $categoryIds = array_map('intval', $product->getCategoryIds() ?: []);

        if ($this->hasConfiguredCategory($categoryIds, $this->config->getHighRiskCategoryIds($storeId))) {
            $score += $this->config->getHighRiskCategoryPenalty($storeId);
            $reasons[] = 'This product belongs to a category with historically higher return pressure.';
            $recommendations[] = 'Review imagery, FAQ content, and expectation-setting copy for this category.';
        }

        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $score += $this->config->getConfigurablePenalty($storeId);
            $reasons[] = 'Variant selection increases mismatch risk before the order is placed.';
            $recommendations[] = 'Guide the shopper toward the right size, color, or configuration before checkout.';
        }

        $isFashion = $this->hasConfiguredCategory($categoryIds, $this->config->getFashionCategoryIds($storeId));
        $isElectronics = $this->hasConfiguredCategory($categoryIds, $this->config->getElectronicsCategoryIds($storeId));

        if ($isFashion && $this->isEmptyProductValue($product, 'return_size_guidance')
            && $this->isEmptyProductValue($product, 'size_chart')
        ) {
            $score += $this->config->getSizeGuidancePenalty($storeId);
            $reasons[] = 'Size guidance is missing, which can cause fit-related returns.';
            $recommendations[] = 'Add fit notes, body measurements, or a clear size-chart recommendation.';
        }

        if ($isFashion && $this->isEmptyProductValue($product, 'material')) {
            $score += $this->config->getMaterialPenalty($storeId);
            $reasons[] = 'Material details are missing, so shoppers may misread texture or drape.';
            $recommendations[] = 'Add material and feel details to reduce expectation mismatch.';
        }

        if ($isElectronics && $this->isEmptyProductValue($product, 'return_compatibility_notes')) {
            $score += $this->config->getCompatibilityPenalty($storeId);
            $reasons[] = 'Compatibility guidance is missing for a product that may depend on setup or fit.';
            $recommendations[] = 'Document supported devices, dimensions, or installation requirements.';
        }

        if ($customerGroupId === GroupInterface::NOT_LOGGED_IN_ID && $product->getTypeId() === Configurable::TYPE_CODE) {
            $score += $this->config->getGuestPenalty($storeId);
            $reasons[] = 'Guest shoppers have less context saved, so guidance matters more here.';
            $recommendations[] = 'Highlight the best-match option before the guest reaches checkout.';
        }

        $ratingSummary = $this->loadRatingSummary($product);
        if ($ratingSummary !== null && $ratingSummary < $this->config->getLowRatingThreshold($storeId)) {
            $score += $this->config->getLowRatingPenalty($storeId);
            $reasons[] = sprintf(
                'The current rating summary is %d/100, which suggests expectation gaps.',
                $ratingSummary
            );
            $recommendations[] = 'Audit recent feedback and update content around the most common complaint.';
        }

        $matchedKeywords = $this->findReviewKeywords((int)$product->getId(), $storeId);
        if ($matchedKeywords !== []) {
            $score += $this->config->getNegativeReviewPenalty($storeId);
            $reasons[] = 'Recent reviews mention return-related concerns: ' . implode(', ', $matchedKeywords) . '.';
            $recommendations[] = 'Surface the concern early on PDP so shoppers can self-qualify before purchase.';
        }

        $overrideNote = trim((string)$product->getData('return_risk_override_note'));
        if ($overrideNote !== '') {
            $reasons[] = $overrideNote;
        }

        $score = min(100, $score);
        $reasons = array_values(array_unique($reasons));
        $recommendations = array_values(array_unique($recommendations));

        return new RiskAnalysis(
            $score,
            $this->resolveLabel($score, $storeId),
            array_slice($reasons, 0, 4),
            array_slice($recommendations, 0, 3)
        );
    }

    private function loadRatingSummary(ProductInterface $product): ?int
    {
        $currentValue = $product->getRatingSummary();
        if (is_numeric($currentValue)) {
            return (int)$currentValue;
        }

        if ($currentValue instanceof DataObject) {
            $value = $currentValue->getData('rating_summary');
            return is_numeric($value) ? (int)$value : null;
        }

        if (!$product instanceof Product) {
            return null;
        }

        try {
            $productForSummary = clone $product;
            $this->appendSummaryDataFactory->create()->execute(
                $productForSummary,
                (int)($product->getStoreId() ?: $this->storeManager->getStore()->getId()),
                Review::ENTITY_PRODUCT_CODE
            );
        } catch (\Throwable) {
            return null;
        }

        $value = $productForSummary->getRatingSummary();
        return is_numeric($value) ? (int)$value : null;
    }

    /**
     * @return string[]
     */
    private function findReviewKeywords(int $productId, int $storeId): array
    {
        if ($productId <= 0) {
            return [];
        }

        $keywords = $this->config->getNegativeReviewKeywords($storeId);
        if ($keywords === []) {
            return [];
        }

        $collection = $this->reviewCollectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->addStatusFilter(Review::STATUS_APPROVED);
        $collection->addEntityFilter('product', $productId);
        $collection->setDateOrder();
        $collection->setPageSize(5);
        $collection->setCurPage(1);

        $matched = [];
        foreach ($collection as $review) {
            $detail = mb_strtolower((string)$review->getData('detail'));
            foreach ($keywords as $keyword) {
                if (str_contains($detail, $keyword)) {
                    $matched[] = $keyword;
                }
            }
        }

        return array_values(array_unique($matched));
    }

    /**
     * @param int[] $productCategoryIds
     * @param int[] $configuredCategoryIds
     */
    private function hasConfiguredCategory(array $productCategoryIds, array $configuredCategoryIds): bool
    {
        return array_intersect($productCategoryIds, $configuredCategoryIds) !== [];
    }

    private function isEmptyProductValue(ProductInterface $product, string $attributeCode): bool
    {
        $value = $product->getData($attributeCode);
        if ($value === null) {
            return true;
        }

        if (is_array($value)) {
            return $value === [];
        }

        return trim((string)$value) === '';
    }

    private function resolveLabel(int $score, int $storeId): string
    {
        if ($score >= $this->config->getHighThreshold($storeId)) {
            return 'High';
        }

        if ($score >= $this->config->getMediumThreshold($storeId)) {
            return 'Medium';
        }

        return 'Low';
    }
}
