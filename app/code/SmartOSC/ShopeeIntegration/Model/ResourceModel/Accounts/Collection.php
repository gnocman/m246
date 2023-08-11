<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SmartOSC\ShopeeIntegration\Model\Accounts;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Collection model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Accounts::class, ResourceModel::class);
    }
}
