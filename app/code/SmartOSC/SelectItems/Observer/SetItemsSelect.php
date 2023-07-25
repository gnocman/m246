<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SetItemsSelect implements ObserverInterface
{
    /**
     * Observer for sales_quote_address_collect_totals_before
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $shippingAssignment = $observer->getShippingAssignment();
        $address = $shippingAssignment->getShipping()->getAddress();

        $allItems = $address->getAllItems();
        $itemsSelect = [];
        foreach ($allItems as $item) {
            if ($item->getData('is_select')) {
                $itemsSelect[] = $item;
            }
        }

        $shippingAssignment->setItems($itemsSelect);
    }
}
