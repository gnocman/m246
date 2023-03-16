<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test3\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class of Observer Change Display Text
 */
class ChangeDisplayText implements ObserverInterface
{
    /**
     * Observer Change Display Text
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        $customer = $observer->getData('customer');
        $customer->setData('firstname', 'Perfect');
    }
}
