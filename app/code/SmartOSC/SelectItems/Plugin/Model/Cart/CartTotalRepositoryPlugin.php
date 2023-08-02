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
    private Data $dataHelper;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param ItemConverter $itemConverter
     * @param Data $dataHelper
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ItemConverter $itemConverter,
        Data $dataHelper
    ) {
        $this->cartRepository = $cartRepository;
        $this->itemConverter = $itemConverter;
        $this->dataHelper = $dataHelper;
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
        if (!$this->dataHelper->isEnabled()) {
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
}
