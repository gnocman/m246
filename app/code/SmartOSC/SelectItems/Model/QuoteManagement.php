<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Model;

use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Quote\Model\ResourceModel\Quote\Item;

class QuoteManagement extends \Magento\Quote\Model\QuoteManagement
{
    /**
     * Convert quote items to order items for quote
     *
     * @param QuoteEntity $quote
     * @return array
     */
    protected function resolveItems(QuoteEntity $quote)
    {
        $orderItems = [];
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($quoteItem->getData('is_select')) {
                $itemId = $quoteItem->getId();

                if (!empty($orderItems[$itemId])) {
                    continue;
                }

                $parentItemId = $quoteItem->getParentItemId();
                /** @var Item $parentItem */
                if ($parentItemId && !isset($orderItems[$parentItemId])) {
                    $orderItems[$parentItemId] = $this->quoteItemToOrderItem->convert(
                        $quoteItem->getParentItem(),
                        ['parent_item' => null]
                    );
                }
                $parentItem = $orderItems[$parentItemId] ?? null;
                $orderItems[$itemId] = $this->quoteItemToOrderItem->convert($quoteItem, ['parent_item' => $parentItem]);
            }
        }
        return array_values($orderItems);
    }
}
