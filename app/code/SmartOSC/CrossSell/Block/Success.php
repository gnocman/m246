<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\CrossSell\Block;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;

/**
 * Get CrossSell Products in Success Page
 */
class Success extends Template
{
    /**
     * @var Session
     */
    protected Session $_checkoutSession;
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $_productCollectionFactory;
    /**
     * @var ImageFactory
     */
    private ImageFactory $imageFactory;

    /**
     * @param Template\Context $context
     * @param Session $checkoutSession
     * @param CollectionFactory $productCollectionFactory
     * @param ImageFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        CollectionFactory $productCollectionFactory,
        ImageFactory $imageFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
        $this->imageFactory = $imageFactory;
    }

    /**
     * Get CrossSell Products
     *
     * @return Collection
     */
    public function getCrossSellProducts(): Collection
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $items = $order->getAllVisibleItems();
        $productIds = [];
        foreach ($items as $item) {
            $product = $item->getProduct();
            $crossSellProducts = $product->getCrossSellProductCollection();
            foreach ($crossSellProducts as $crossSellProduct) {
                $productIds[] = $crossSellProduct->getId();
            }
        }

        return $this->_productCollectionFactory
            ->create()
            ->addIdFilter($productIds)
            ->addAttributeToSelect('*')
            ->setPageSize(4)
            ->load();
    }

    /**
     * Get Image product
     *
     * @param Product $product
     * @param String $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage(Product $product, string $imageId, array $attributes = []): Image
    {
        return $this->imageFactory->create($product, $imageId, $attributes);
    }

    /**
     * Get Price
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function getPriceRender(): mixed
    {
        return $this->getLayout()->getBlock('product.price.render.default')->setData('is_product_list', true);
    }

    /**
     * Get product price
     *
     * @param Product $product
     * @return string
     * @throws LocalizedException
     */
    public function getProductPrice(Product $product): string
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }
}
