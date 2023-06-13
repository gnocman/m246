<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use SmartOSC\GroupOrder\Api\GroupOrderRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;

class GroupOrderRepository implements GroupOrderRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    protected QuoteFactory $quoteFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @var Cart
     */
    private Cart $cart;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @param QuoteFactory $quoteFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CartRepositoryInterface $cartRepository
     * @param Cart $cart
     * @param Session $customerSession
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
        CartRepositoryInterface $cartRepository,
        Cart $cart,
        Session $customerSession
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->cart = $cart;
        $this->customerSession = $customerSession;
    }

    /**
     * @param $groupOrderToken
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function group($groupOrderToken)
    {
        // Check if customer is logged in
        if (!$this->customerSession->isLoggedIn()) {
            throw new LocalizedException(__('You must be logged in to add items.'));
        } else {
            $this->cart->save();
        }

        // Check if items have already been added
        if ($this->customerSession->getItemsAddedFlag()) {
            return $this->cartRepository->get($this->cart->getQuote()->getId());
        }

        /** @var Quote $quote */
        $quote = $this->getQuoteByGroupOrderToken($groupOrderToken);
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if (!$item->getParentItemId()) {
                try {
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $info = $options['info_buyRequest'];
                    $info['qty'] = $item->getQty();

                    $this->cart->addProduct($item->getProduct(), $info);
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(__('Cannot add product to cart'));
                }
            }
        }
        $this->cart->save();

        // Set the flag indicating that items have been added
        $this->customerSession->setItemsAddedFlag(true);

        return $this->cartRepository->get($this->cart->getQuote()->getId());
    }

    /**
     * Get quote by group order token
     *
     * @param string $groupOrderToken
     * @return Quote
     * @throws LocalizedException
     */
    protected function getQuoteByGroupOrderToken($groupOrderToken)
    {
        $quote = $this->quoteFactory->create()->load($groupOrderToken, 'order_cart_token');
        if (!$quote->getId()) {
            throw new LocalizedException(__('The Cart is not available'));
        }

        return $quote;
    }
}
