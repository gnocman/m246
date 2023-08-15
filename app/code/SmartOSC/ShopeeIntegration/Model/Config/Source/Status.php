<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('Disable')
            ],
            [
                'value' => '1',
                'label' => __('Enable')
            ],
        ];
    }
}
