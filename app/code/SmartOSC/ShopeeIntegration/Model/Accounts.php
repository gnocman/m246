<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model;

use Magento\Framework\Model\AbstractModel;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts as ResourceModel;

class Accounts extends AbstractModel
{
    /**
     * Init ResourceModel
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
