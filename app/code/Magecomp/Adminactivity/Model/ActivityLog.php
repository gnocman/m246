<?php

namespace Magecomp\Adminactivity\Model;

use \Magento\Framework\Model\AbstractModel;


class ActivityLog extends AbstractModel
{
    /**
     * @var string
     */
    const ACTIVITYLOG_ID = 'entity_id'; // We define the id fieldname

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magecomp\Adminactivity\Model\ResourceModel\ActivityLog');
    }
}
