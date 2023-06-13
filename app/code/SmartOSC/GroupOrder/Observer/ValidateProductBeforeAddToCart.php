<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class ValidateProductBeforeAddToCart implements ObserverInterface
{
    /**
     * @var Session
     */
    protected Session $customerSession;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @param Session $customerSession
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Session $customerSession,
        ManagerInterface $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
    }

    /**
     * Check customer login add to cart
     *
     * @param Observer $observer
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();

        if ($request->getParam('key')) {
            if (!$this->customerSession->isLoggedIn()) {
                $this->messageManager->addErrorMessage('You must be logged in to add items.');
                $observer->getRequest()->setParam('product', false);
            }
        } else {
            return $this;
        }

        return $this;
    }
}
