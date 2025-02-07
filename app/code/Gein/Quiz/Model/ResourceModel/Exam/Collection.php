<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model\ResourceModel\Exam;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'exam_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Gein\Quiz\Model\Exam::class,
            \Gein\Quiz\Model\ResourceModel\Exam::class
        );
    }

    public function getQuestionsByExamId($examId)
    {
        $this->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(
                [
                    'exam_id' => 'main_table.exam_id',
                    'name' => 'main_table.name',
                    'testing_date' => 'main_table.testing_date',
                    'total_time' => 'main_table.total_time',
                    'description' => 'main_table.description',
                    'status' => 'main_table.status',
                    'question_id' => 'gein_quiz_question.question_id',
                    'type' => 'gein_quiz_question.type',
                    'content' => 'gein_quiz_question.content',
                    'image' => 'gein_quiz_question.image'
                ]
            )->joinLeft('gein_quiz_question',
                'main_table.exam_id = gein_quiz_question.exam_id',
                [])
            ->where('main_table.exam_id = ?', $examId);
        return $this;
    }
}

