<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\AnswerInterface;
use Magento\Framework\Model\AbstractModel;

class Answer extends AbstractModel implements AnswerInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Gein\Quiz\Model\ResourceModel\Answer::class);
    }

    /**
     * @inheritDoc
     */
    public function getAnswerId()
    {
        return $this->getData(self::ANSWER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAnswerId($answerId)
    {
        return $this->setData(self::ANSWER_ID, $answerId);
    }

    /**
     * @inheritDoc
     */
    public function getQuestionId()
    {
        return $this->getData(self::QUESTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionId($questionId)
    {
        return $this->setData(self::QUESTION_ID, $questionId);
    }

    /**
     * @inheritDoc
     */
    public function getQuestionType()
    {
        return $this->getData(self::QUESTION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionType($type)
    {
        return $this->setData(self::QUESTION_TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getIsCorrect()
    {
        return $this->getData(self::IS_CORRECT);
    }

    /**
     * @inheritDoc
     */
    public function setIsCorrect($isCorrect)
    {
        return $this->setData(self::IS_CORRECT, $isCorrect);
    }
}

