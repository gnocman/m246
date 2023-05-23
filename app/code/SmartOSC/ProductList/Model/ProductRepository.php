<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\ProductList\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ProductRepository implements \SmartOSC\ProductList\Api\ProductRepositoryInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected ProductSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * ProductRepository constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        ProductSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Retrieve simple products with pagination.
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getList($currentPage, $pageSize)
    {
        $this->searchCriteriaBuilder->addFilter('type_id', ProductType::TYPE_SIMPLE);
        $this->searchCriteriaBuilder->setCurrentPage($currentPage);
        $this->searchCriteriaBuilder->setPageSize($pageSize);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->productRepository->getList($searchCriteria);
    }
}
