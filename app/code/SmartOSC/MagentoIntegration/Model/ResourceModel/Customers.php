<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Customers extends AbstractDb
{
    /**
     * Init table & id table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_integration_customers', 'entity_id');
    }
}
