<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\ExamInterface;
use Magento\Framework\Model\AbstractModel;

class Exam extends AbstractModel implements ExamInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Gein\Quiz\Model\ResourceModel\Exam::class);
    }

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
    public function getCreateTime()
    {
        return $this->getData(self::CREATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setCreateTime($createTime)
    {
        return $this->setData(self::CREATE_TIME, $createTime);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
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
}

