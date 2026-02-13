<?php

namespace Magecomp\Adminactivity\Model\ResourceModel\Login;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magecomp\Adminactivity\Model\ResourceModel\Login
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Magecomp\Adminactivity\Model\Login',
            'Magecomp\Adminactivity\Model\ResourceModel\Login'
        );
    }
}
