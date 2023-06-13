<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Interface GroupOrderRepositoryInterface
 * @api
 */
interface GroupOrderRepositoryInterface
{
    /**
     * Required($groupOrderToken)
     *
     * @param string|null $groupOrderToken
     * @return CartInterface
     * @throws LocalizedException
     */
    public function group($groupOrderToken);
}
