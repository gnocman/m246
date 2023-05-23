<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dev\RestApi\Model\Api;

use Dev\RestApi\Api\ProductRepositoryInterface;
use Dev\RestApi\Api\RequestItemInterfaceFactory;
use Dev\RestApi\Api\ResponseItemInterfaceFactory;
use Dev\RestApi\Api\RequestItemInterface;
use Dev\RestApi\Api\ResponseItemInterface;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var Action
     */
    private Action $productAction;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;

    /**
     * @var RequestItemInterfaceFactory
     */
    private RequestItemInterfaceFactory $requestItemFactory;

    /**
     * @var ResponseItemInterfaceFactory
     */
    private ResponseItemInterfaceFactory $responseItemFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Action $productAction
     * @param CollectionFactory $productCollectionFactory
     * @param RequestItemInterfaceFactory $requestItemFactory
     * @param ResponseItemInterfaceFactory $responseItemFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Action $productAction,
        CollectionFactory $productCollectionFactory,
        RequestItemInterfaceFactory $requestItemFactory,
        ResponseItemInterfaceFactory $responseItemFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->productAction = $productAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->requestItemFactory = $requestItemFactory;
        $this->responseItemFactory = $responseItemFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     *
     * @param int $id
     * @return ResponseItemInterface
     * @throws NoSuchEntityException
     */
    public function getItem(int $id): mixed
    {
        $collection = $this->getProductCollection()
            ->addAttributeToFilter('entity_id', ['eq' => $id]);

        /** @var ProductInterface $product */
        $product = $collection->getFirstItem();
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product not found'));
        }

        return $this->getResponseItemFromProduct($product);
    }

    /**
     * @inheritDoc
     *
     * @param RequestItemInterface[] $products
     * @return void
     * @throws NoSuchEntityException
     */
    public function setDescription(array $products): void
    {
        foreach ($products as $product) {
            $this->setDescriptionForProduct(
                $product->getId(),
                $product->getDescription()
            );
        }
    }

    /**
     * Get getProductCollection
     *
     * @return Collection
     */
    private function getProductCollection(): mixed
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();

        $collection
            ->addAttributeToSelect(
                [
                    'entity_id',
                    ProductInterface::SKU,
                    ProductInterface::NAME,
                    'description'
                ],
                'left'
            );

        return $collection;
    }

    /**
     * Get getResponseItemFromProduct
     *
     * @param ProductInterface $product
     * @return ResponseItemInterface
     */
    private function getResponseItemFromProduct(ProductInterface $product): mixed
    {
        /** @var ResponseItemInterface $responseItem */
        $responseItem = $this->responseItemFactory->create();

        $responseItem->setId($product->getId())
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setDescription($product->getDescription());

        return $responseItem;
    }

    /**
     * Set the description for the product.
     *
     * @param int $id
     * @param string $description
     * @return void
     * @throws NoSuchEntityException
     */
    private function setDescriptionForProduct(int $id, string $description): void
    {
        $this->productAction->updateAttributes(
            [$id],
            ['description' => $description],
            $this->storeManager->getStore()->getId()
        );
    }
}
