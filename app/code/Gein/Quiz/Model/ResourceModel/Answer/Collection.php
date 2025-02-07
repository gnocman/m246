<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model\ResourceModel\Answer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'answer_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Gein\Quiz\Model\Answer::class,
            \Gein\Quiz\Model\ResourceModel\Answer::class
        );
    }
}

