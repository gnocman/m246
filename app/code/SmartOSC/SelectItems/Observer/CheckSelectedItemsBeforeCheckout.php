<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;

class CheckSelectedItemsBeforeCheckout implements ObserverInterface
{
    public const CART_PAGE_URL = '/checkout/cart/index';

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @param CheckoutSession $checkoutSession
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        ManagerInterface $messageManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
    }

    /**
     * Events of controller_action_predispatch_checkout_index_index
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllItems();

        foreach ($items as $item) {
            if ($item->getIsSelect()) {
                return;
            }
        }

        $this->messageManager->addErrorMessage(
            __('In order to proceed with the payment, you must select the items first.')
        );

        $controller = $observer->getControllerAction();
        $controller->getResponse()->setRedirect(self::CART_PAGE_URL);
    }
}
