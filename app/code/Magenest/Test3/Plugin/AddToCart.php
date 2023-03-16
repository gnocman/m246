<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test3\Plugin;

use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Checkout\Model\Cart;

/**
 * Plugin class to modify the AddProduct method of the Cart class
 */
class AddToCart
{
    /**
     * @var Configurable
     */
    protected Configurable $configurableProduct;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $product;

    /**
     * @param Configurable $configurableProduct
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Configurable $configurableProduct,
        ProductFactory $productFactory
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->product = $productFactory;
    }

    /**
     * Plugin method to modify the AddProduct method of the Cart class
     *
     * @param Cart $subject
     * @param $productInfo
     * @param $requestInfo
     * @return array
     */
    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo)
    {
        $data = $productInfo->getTypeId(); // == getData('type_id');

        if ($data == 'configurable') {
            $attributes = $requestInfo['super_attribute'];
            $product = $this->product->create()->load($productInfo->getId());
            $new_product = $this->configurableProduct->getProductByAttributes($attributes, $product);
        } else {

            return [$productInfo, $requestInfo];
        }

        return [$new_product, $requestInfo];
    }
}
