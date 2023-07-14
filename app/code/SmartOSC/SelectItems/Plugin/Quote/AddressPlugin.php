<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Plugin\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Item;

class AddressPlugin
{
    /**
     * Plugin of Magento\Quote\Model\Quote\Address
     *
     * @param Address $subject
     * @param Item[] $result
     * @return Item[]
     */
    public function afterGetAllItems(Address $subject, array $result)
    {
        $itemsSelect = [];
        foreach ($result as $itemSelect) {
            if ($itemSelect->getData('is_select') == 1) {
                $itemsSelect[] = $itemSelect;
            }
        }

        return $itemsSelect;
    }
}
