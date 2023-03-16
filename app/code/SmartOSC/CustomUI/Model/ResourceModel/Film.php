<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CustomUI\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Init table & id table
 */
class Film extends AbstractDb
{
    /**
     * Init table & id table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('smart_film', 'film_id');
    }
}
