<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Levince\PricingPack\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class IsEnable extends AbstractHelper
{
    public const CONFIG_MODULE_PATH = 'levince_pricing_pack/general/enable';

    /**
     * Get isEnabled
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH, ScopeInterface::SCOPE_STORE);
    }
}
