<?php

namespace Magecomp\Adminactivity\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Login
 * @package Magecomp\Adminactivity\Model\ResourceModel
 */
class Login extends AbstractDb
{

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('magecomp_admin_login_activity', 'entity_id');
    }
}
