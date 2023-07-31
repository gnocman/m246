<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Plugin\Model\Cart;

use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\CartTotalRepository;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use SmartOSC\SelectItems\Helper\Data;
use Magento\Framework\App\RequestInterface;

class CartTotalRepositoryPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;
    /**
     * @var ItemConverter
     */
    private ItemConverter $itemConverter;
    /**
     * @var Data
     */
    private Data $isEnabledHelper;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param ItemConverter $itemConverter
     * @param Data $isEnabledHelper
     * @param RequestInterface $request
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ItemConverter $itemConverter,
        Data $isEnabledHelper,
        RequestInterface $request
    ) {
        $this->cartRepository = $cartRepository;
        $this->itemConverter = $itemConverter;
        $this->isEnabledHelper = $isEnabledHelper;
        $this->request = $request;
    }

    /**
     * Plugin of Magento\Quote\Model\Cart\CartTotalRepository
     *
     * @param CartTotalRepository $subject
     * @param TotalsInterface $result
     * @param int $cartId
     * @return TotalsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(CartTotalRepository $subject, TotalsInterface $result, $cartId)
    {
        if (!$this->isEnabledHelper->isEnabled()) {
            return $result;
        }

        if (!$this->isMatchingPath()) {
            return $result;
        }

        $quote = $this->cartRepository->getActive($cartId);
        $allItems = $quote->getAllVisibleItems();
        $itemsSelect = [];
        $totalQty = 0;

        foreach ($allItems as $item) {
            if ($item->getData('is_select')) {
                $itemsSelect[] = $this->itemConverter->modelToDataObject($item);
                $totalQty += $item->getQty();
            }
        }

        $result->setItems($itemsSelect);
        $result->setItemsQty($totalQty);

        return $result;
    }

    /**
     * Check if the current request path matches the specified paths.
     *
     * @return bool
     */
    public function isMatchingPath()
    {
        return strcmp($this->request->getPathInfo(), '/checkout/') === 0;
    }
}
