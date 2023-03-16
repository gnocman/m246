<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 *
 */
class Relation implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '1',
                'label' => __('Show')
            ],
            [
                'value' => '2',
                'label' => __('Not-Show')
            ],
        ];
    }
}
