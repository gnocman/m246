<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomizeAdminhtml\Observer;

use Magenest\CustomizeAdminhtml\Helper\Email;
use Magento\Framework\Event\ObserverInterface;

/**
 *
 */
class CustomerRegisterObserver implements ObserverInterface
{
    /**
     * @var Email
     */
    private Email $helperEmail;

    /**
     * @param Email $helperEmail
     */
    public function __construct(
        Email $helperEmail
    ) {
        $this->helperEmail = $helperEmail;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return null
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return $this->helperEmail->sendEmail();
    }
}
