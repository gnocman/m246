<?php

namespace Magecomp\Adminactivity\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class ActivityLogDetail
 * @package Magecomp\Adminactivity\Model
 */
class ActivityLogDetail extends AbstractModel
{
    /**
     * @var string
     */
    const ACTIVITYLOGDETAIL_ID = 'entity_id'; // We define the id fieldname

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magecomp\Adminactivity\Model\ResourceModel\ActivityLogDetail');
    }
}
