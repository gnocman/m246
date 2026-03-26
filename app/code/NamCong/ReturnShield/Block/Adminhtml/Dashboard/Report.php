<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Block\Adminhtml\Dashboard;

use NamCong\ReturnShield\Model\Config;
use NamCong\ReturnShield\Service\RiskAnalyzer;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Report extends Template
{
    private const DASHBOARD_SCAN_BATCH_SIZE = 200;

    protected $_template = 'NamCong_ReturnShield::dashboard/report.phtml';

    public function __construct(
        Context $context,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly RiskAnalyzer $riskAnalyzer,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRows(): array
    {
        if (!$this->config->isEnabled()) {
            return [];
        }

        $storeId = (int)$this->storeManager->getStore()->getId();
        $rows = [];
        $currentPage = 1;
        $lastPageNumber = 1;
        $displayLimit = $this->config->getMaxDashboardProducts($storeId);
        $mediumThreshold = $this->config->getMediumThreshold($storeId);

        do {
            $collection = $this->createDashboardCollection($storeId);
            $collection->setPageSize(self::DASHBOARD_SCAN_BATCH_SIZE);
            $collection->setCurPage($currentPage);
            $lastPageNumber = (int)$collection->getLastPageNumber();

            foreach ($collection as $product) {
                $analysis = $this->riskAnalyzer->analyze($product);
                if ($analysis->getScore() < $mediumThreshold) {
                    continue;
                }

                $rows[] = [
                    'sku' => (string)$product->getSku(),
                    'name' => (string)$product->getName(),
                    'score' => $analysis->getScore(),
                    'label' => $analysis->getLabel(),
                    'reasons' => $analysis->getReasons(),
                    'recommendations' => $analysis->getRecommendations()
                ];

                usort(
                    $rows,
                    static fn(array $left, array $right): int => $right['score'] <=> $left['score']
                );

                if (count($rows) > $displayLimit) {
                    array_pop($rows);
                }
            }

            $collection->clear();
            $currentPage++;
        } while ($currentPage <= $lastPageNumber);

        return $rows;
    }

    private function createDashboardCollection(int $storeId): \Magento\Catalog\Model\ResourceModel\Product\Collection
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect([
            'name',
            'sku',
            'material',
            'return_risk_manual_adjustment',
            'return_risk_override_note',
            'return_size_guidance',
            'return_compatibility_notes',
            'size_chart'
        ]);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->addAttributeToSort('entity_id', 'ASC');

        return $collection;
    }
}
