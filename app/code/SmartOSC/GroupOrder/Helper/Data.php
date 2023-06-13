<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Helper;

use Mageplaza\Core\Helper\AbstractData;

class Data extends AbstractData
{
    public const CONFIG_MODULE_PATH = 'sharecart';

    /**
     * Get isDisabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }
}
