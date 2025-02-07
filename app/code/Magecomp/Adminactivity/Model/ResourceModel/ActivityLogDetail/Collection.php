<?php

namespace Magecomp\Adminactivity\Model\ResourceModel\ActivityLogDetail;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magecomp\Adminactivity\Model\ResourceModel\ActivityLogDetail
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
            'Magecomp\Adminactivity\Model\ActivityLogDetail',
            'Magecomp\Adminactivity\Model\ResourceModel\ActivityLogDetail'
        );
    }
}
