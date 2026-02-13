<?php

namespace Magecomp\Adminactivity\Model\ResourceModel\Activity;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magecomp\Adminactivity\Model\ResourceModel\Activity
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Magecomp\Adminactivity\Model\Activity',
            'Magecomp\Adminactivity\Model\ResourceModel\Activity'
        );
    }
}
