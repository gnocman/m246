<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Accounts extends AbstractDb
{
    /**
     * Init table & id table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('shopee_integration_accounts', 'account_id');
    }
}
