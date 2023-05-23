<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\ProductList\Api;

use Magento\Catalog\Api\Data\ProductSearchResultsInterface;

interface ProductRepositoryInterface
{
    /**
     * Retrieve simple products with pagination.
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return ProductSearchResultsInterface
     */
    public function getList($currentPage, $pageSize);
}
