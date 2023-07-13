<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Model\Quote;

class Address extends \Magento\Quote\Model\Quote\Address
{
    private const CACHED_ITEMS_ALL = 'cached_items_all';

    /**
     * Get all available address items
     *
     * @return \Magento\Quote\Model\Quote\Address\Item[]|mixed|null
     */
    public function getAllItems()
    {
        // We calculate item list once and cache it in three arrays - all items
        if (!$this->hasData(self::CACHED_ITEMS_ALL)) {
            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $itemsSelect = [];
            foreach ($quoteItems as $itemSelect) {
                if ($itemSelect->getIsSelect() == 1) {
                    $itemsSelect[] = $itemSelect;
                }
            }

            $items = [];
            if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }
                    $items[] = $aItem;
                }
            } else {
                /*
                 * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                 */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? $addressType == self::TYPE_BILLING
                    : $addressType == self::TYPE_SHIPPING;

                if ($canAddItems) {
                    foreach ($itemsSelect as $qItem) {
                        if ($qItem->isDeleted()) {
                            continue;
                        }
                        $items[] = $qItem;
                    }
                }
            }

            // Cache calculated lists
            $this->setData(self::CACHED_ITEMS_ALL, $items);
        }

        return $this->getData(self::CACHED_ITEMS_ALL);
    }
}
