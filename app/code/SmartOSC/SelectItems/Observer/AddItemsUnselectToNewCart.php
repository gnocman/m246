<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;

class AddItemsUnselectToNewCart implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;
    /**
     * @var ProductFactory
     */
    private ProductFactory $productFactory;

    /**
     * @param QuoteFactory $quoteFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        ProductFactory $productFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Events of checkout_submit_all_after
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $items = $quote->getItems();
        $customerId = $quote->getCustomerId();

        $unselectedProducts = [];

        foreach ($items as $item) {
            if (!$item->getIsSelect()) {
                $productId = $item->getProduct()->getId();

                $product = $this->productFactory->create()->load($productId);
                $unselectedProducts[] = $product;
            }
        }

        if (!empty($unselectedProducts)) {
            $quoteNew = $this->quoteFactory->create();

            $quoteNew->setCustomerId($customerId);
            $quoteNew->setStoreId($quote->getStoreId());
            $quoteNew->setIsActive(true);
            $quoteNew->setCurrency($quote->getCurrency());
            $quoteNew->setCustomer($quote->getCustomer());
            $quoteNew->setCustomerIsGuest($quote->getCustomerIsGuest());
            $quoteNew->setCustomerEmail($quote->getCustomerEmail());
            $quoteNew->setCustomerGroupId($quote->getCustomerGroupId());
            $quoteNew->setTotalsCollectedFlag(false);

            foreach ($unselectedProducts as $product) {
                $oldProduct = $quote->getItemByProduct($product);
                $qty = $oldProduct ? $oldProduct->getQty() : 1;

                $quoteNew->addProduct($product, $qty);
            }

            foreach ($quote->getAllAddresses() as $address) {
                $quoteNew->addAddress($address);
            }

            $quoteNew->collectTotals()->save();
            $quoteNew->setIsActive(true);
        }
    }
}
