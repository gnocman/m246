<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'returnshield/general/enabled';
    private const XML_PATH_MEDIUM_THRESHOLD = 'returnshield/general/medium_threshold';
    private const XML_PATH_HIGH_THRESHOLD = 'returnshield/general/high_threshold';
    private const XML_PATH_MAX_DASHBOARD_PRODUCTS = 'returnshield/general/max_dashboard_products';
    private const XML_PATH_HIGH_RISK_CATEGORY_IDS = 'returnshield/rules/high_risk_category_ids';
    private const XML_PATH_FASHION_CATEGORY_IDS = 'returnshield/rules/fashion_category_ids';
    private const XML_PATH_ELECTRONICS_CATEGORY_IDS = 'returnshield/rules/electronics_category_ids';
    private const XML_PATH_HIGH_RISK_CATEGORY_PENALTY = 'returnshield/rules/high_risk_category_penalty';
    private const XML_PATH_CONFIGURABLE_PENALTY = 'returnshield/rules/configurable_penalty';
    private const XML_PATH_SIZE_GUIDANCE_PENALTY = 'returnshield/rules/size_guidance_penalty';
    private const XML_PATH_MATERIAL_PENALTY = 'returnshield/rules/material_penalty';
    private const XML_PATH_COMPATIBILITY_PENALTY = 'returnshield/rules/compatibility_penalty';
    private const XML_PATH_GUEST_PENALTY = 'returnshield/rules/guest_penalty';
    private const XML_PATH_LOW_RATING_THRESHOLD = 'returnshield/rules/low_rating_threshold';
    private const XML_PATH_LOW_RATING_PENALTY = 'returnshield/rules/low_rating_penalty';
    private const XML_PATH_NEGATIVE_REVIEW_KEYWORDS = 'returnshield/rules/negative_review_keywords';
    private const XML_PATH_NEGATIVE_REVIEW_PENALTY = 'returnshield/rules/negative_review_penalty';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMediumThreshold(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_MEDIUM_THRESHOLD, $storeId, 40);
    }

    public function getHighThreshold(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_HIGH_THRESHOLD, $storeId, 70);
    }

    public function getMaxDashboardProducts(?int $storeId = null): int
    {
        return max(1, $this->getInt(self::XML_PATH_MAX_DASHBOARD_PRODUCTS, $storeId, 25));
    }

    /**
     * @return int[]
     */
    public function getHighRiskCategoryIds(?int $storeId = null): array
    {
        return $this->getIntList(self::XML_PATH_HIGH_RISK_CATEGORY_IDS, $storeId);
    }

    /**
     * @return int[]
     */
    public function getFashionCategoryIds(?int $storeId = null): array
    {
        return $this->getIntList(self::XML_PATH_FASHION_CATEGORY_IDS, $storeId);
    }

    /**
     * @return int[]
     */
    public function getElectronicsCategoryIds(?int $storeId = null): array
    {
        return $this->getIntList(self::XML_PATH_ELECTRONICS_CATEGORY_IDS, $storeId);
    }

    public function getHighRiskCategoryPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_HIGH_RISK_CATEGORY_PENALTY, $storeId, 20);
    }

    public function getConfigurablePenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_CONFIGURABLE_PENALTY, $storeId, 10);
    }

    public function getSizeGuidancePenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_SIZE_GUIDANCE_PENALTY, $storeId, 20);
    }

    public function getMaterialPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_MATERIAL_PENALTY, $storeId, 10);
    }

    public function getCompatibilityPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_COMPATIBILITY_PENALTY, $storeId, 20);
    }

    public function getGuestPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_GUEST_PENALTY, $storeId, 10);
    }

    public function getLowRatingThreshold(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_LOW_RATING_THRESHOLD, $storeId, 65);
    }

    public function getLowRatingPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_LOW_RATING_PENALTY, $storeId, 15);
    }

    /**
     * @return string[]
     */
    public function getNegativeReviewKeywords(?int $storeId = null): array
    {
        return $this->getStringList(self::XML_PATH_NEGATIVE_REVIEW_KEYWORDS, $storeId);
    }

    public function getNegativeReviewPenalty(?int $storeId = null): int
    {
        return $this->getInt(self::XML_PATH_NEGATIVE_REVIEW_PENALTY, $storeId, 15);
    }

    private function getInt(string $path, ?int $storeId, int $default): int
    {
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
        return is_numeric($value) ? (int)$value : $default;
    }

    /**
     * @return int[]
     */
    private function getIntList(string $path, ?int $storeId): array
    {
        $values = $this->getStringList($path, $storeId);
        return array_values(
            array_map(
                'intval',
                array_filter($values, static fn(string $value): bool => $value !== '')
            )
        );
    }

    /**
     * @return string[]
     */
    private function getStringList(string $path, ?int $storeId): array
    {
        $value = (string)$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*/', trim($value)) ?: [];
        return array_values(
            array_filter(
                array_map(static fn(string $part): string => mb_strtolower(trim($part)), $parts),
                static fn(string $part): bool => $part !== ''
            )
        );
    }
}
