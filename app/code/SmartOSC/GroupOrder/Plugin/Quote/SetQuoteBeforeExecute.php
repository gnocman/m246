<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Quote;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteFactory;

class SetQuoteBeforeExecute
{
    /**
     * @var Cart
     */
    private Cart $cart;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;

    /**
     * @param Cart $cart
     * @param RequestInterface $request
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        Cart $cart,
        RequestInterface $request,
        QuoteFactory $quoteFactory
    ) {
        $this->cart = $cart;
        $this->request = $request;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param Action $subject
     * @return void
     */
    public function beforeExecute(Action $subject)
    {
        $token = $this->request->getParam('key') ?? '';

        $quote = $this->quoteFactory->create()->load($token, 'order_cart_token');
        if ($token) {
            $this->cart->setQuote($quote);
        }
    }
}
