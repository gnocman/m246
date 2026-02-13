<?php

namespace Magecomp\Adminactivity\Model\ResourceModel\ActivityLog;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magecomp\Adminactivity\Model\ResourceModel\ActivityLog
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
            'Magecomp\Adminactivity\Model\ActivityLog',
            'Magecomp\Adminactivity\Model\ResourceModel\ActivityLog'
        );
    }
}
