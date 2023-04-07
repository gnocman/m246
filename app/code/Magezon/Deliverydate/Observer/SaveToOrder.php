<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magezon\Deliverydate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Events Save data to order
 */
class SaveToOrder implements ObserverInterface
{
    /**
     * Events Save data to order
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getQuote();
        $order = $event->getOrder();
        $order->setData('delivery_date', $quote->getData('delivery_date'));
    }
}
