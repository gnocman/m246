<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Dev\RestApi\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface ProductRepositoryInterface
{
    /**
     * Return a filtered product.
     *
     * @param int $id
     * @return ResponseItemInterface
     * @throws NoSuchEntityException
     */
    public function getItem(int $id);

    /**
     * Set descriptions for the products.
     *
     * @param RequestItemInterface[] $products
     * @return void
     */
    public function setDescription(array $products);
}
