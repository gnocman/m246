<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\PriceIndexer\Model\Indexer\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

/**
 * Indexer price
 */
class Price implements ActionInterface, MviewActionInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Execute indexer
     *
     * @param array|null $ids
     * @return void
     */
    public function execute($ids = null): void
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');

        if (!empty($ids)) {
            $productCollection->addFieldToFilter('entity_id', ['in' => $ids]);
        }

        foreach ($productCollection as $product) {
            $oldFinalPrice = $product->getData('price');
            $newFinalPrice = $oldFinalPrice - ($oldFinalPrice * 0.1); // 10% discount
            $product->setData('price', $newFinalPrice);
            $product->setOrigData('price', null);
            $product->getResource()->saveAttribute($product, 'price');
        }
    }

    /**
     * Execute full indexer
     *
     * @return void
     */
    public function executeFull(): void
    {
        $this->execute();
    }

    /**
     * Execute partial indexer by product ids
     *
     * @param array $ids
     * @return void
     */
    public function executeList(array $ids): void
    {
        $this->execute($ids);
    }

    /**
     * Execute indexer for a single product
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id): void
    {
        $this->execute([$id]);
    }
}
