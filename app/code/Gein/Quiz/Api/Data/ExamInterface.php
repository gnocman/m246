<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface ExamInterface
{

    const EXAM_ID = 'exam_id';
    const NAME = 'name';
    const TESTING_DATE = 'testing_date';
    const TOTAL_TIME = 'total_time';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const DESCRIPTION = 'description';
    const STATUS = 'status';

    /**
     * Get exam_id
     * @return string|null
     */
    public function getExamId();

    /**
     * Set exam_id
     * @param string $examId
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setExamId($examId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setName($name);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setDescription($description);

    /**
     * Get testing_date
     * @return string|null
     */
    public function getTestingDate();

    /**
     * Set testing_date
     * @param string $testingDate
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setTestingDate($testingDate);

    /**
     * Get total_time
     * @return int|null
     */
    public function getTotalTime();

    /**
     * Set total_time
     * @param int $totalTime
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setTotalTime($totalTime);

    /**
     * Get create_time
     * @return string|null
     */
    public function getCreateTime();

    /**
     * Set create_time
     * @param string $createTime
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setCreateTime($createTime);

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update_time
     * @param string $updateTime
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Gein\Quiz\Api\Data\ExamInterface
     */
    public function setStatus($status);
}

