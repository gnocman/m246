<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model\ResourceModel\Question;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'question_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Gein\Quiz\Model\Question::class,
            \Gein\Quiz\Model\ResourceModel\Question::class
        );
    }
}

