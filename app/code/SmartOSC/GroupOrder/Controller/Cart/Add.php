<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Controller\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
    }

    /**
     * Add product to shopping cart action
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $token = $this->getRequest()->getParam('key') ?? '';
        if ($token) {
            $this->cart->setQuote($this->quoteFactory->create()->load($token, 'order_cart_token'));
        }

        return parent::execute();
    }
}
