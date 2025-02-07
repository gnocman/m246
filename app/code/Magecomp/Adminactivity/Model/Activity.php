<?php

namespace Magecomp\Adminactivity\Model;

use \Magento\Framework\Model\AbstractModel;


class Activity extends AbstractModel
{
    /**
     * @var string
     */
    const ACTIVITY_ID = 'entity_id'; // We define the id field name

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magecomp\Adminactivity\Model\ResourceModel\Activity');
    }
}
