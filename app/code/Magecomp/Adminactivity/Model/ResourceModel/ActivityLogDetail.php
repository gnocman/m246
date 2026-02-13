<?php

namespace Magecomp\Adminactivity\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ActivityLogDetail
 * @package Magecomp\Adminactivity\Model\ResourceModel
 */
class ActivityLogDetail extends AbstractDb
{

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('magecomp_admin_activity_detail', 'entity_id');
    }
}
