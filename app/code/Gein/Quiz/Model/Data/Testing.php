<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model\Data;

use Gein\Quiz\Api\Data\TestingInterface;
use Magento\Framework\DataObject;

class Testing extends DataObject implements TestingInterface
{

    /**
     * @inheritDoc
     */
    public function getExamId()
    {
        return $this->getData(self::EXAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExamId($examId)
    {
        return $this->setData(self::EXAM_ID, $examId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getTestingDate()
    {
        return $this->getData(self::TESTING_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setTestingDate($testingDate)
    {
        return $this->setData(self::TESTING_DATE, $testingDate);
    }

    /**
     * @inheritDoc
     */
    public function getTotalTime()
    {
        return $this->getData(self::TOTAL_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setTotalTime($totalTime)
    {
        return $this->setData(self::TOTAL_TIME, $totalTime);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getQuestionsList()
    {
        return $this->getData(self::QUESTIONS_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionsList($questionsList)
    {
        return $this->setData(self::QUESTIONS_LIST, $questionsList);
    }
}

