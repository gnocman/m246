<?php

namespace Gein\Quiz\Api\Data;

interface TestingInterface
{
    const EXAM_ID = 'exam_id';
    const NAME = 'name';
    const TESTING_DATE = 'testing_date';
    const TOTAL_TIME = 'total_time';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const QUESTIONS_LIST = 'questions_list';

    /**
     * Get exam_id
     * @return string|null
     */
    public function getExamId();

    /**
     * Set exam_id
     * @param string $examId
     * @return \Gein\Quiz\Api\Data\TestingInterface
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
     * @return \Gein\Quiz\Api\Data\TestingInterface
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
     * @return \Gein\Quiz\Api\Data\TestingInterface
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
     * @return \Gein\Quiz\Api\Data\TestingInterface
     */
    public function setTestingDate($testingDate);

    /**
     * Get total_time
     * @return string|null
     */
    public function getTotalTime();

    /**
     * Set total_time
     * @param string $totalTime
     * @return \Gein\Quiz\Api\Data\TestingInterface
     */
    public function setTotalTime($totalTime);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Gein\Quiz\Api\Data\TestingInterface
     */
    public function setStatus($status);

    /**
     * Get questions_list
     * @return \Gein\Quiz\Api\Data\QuestionInterface[]|null
     */
    public function getQuestionsList();

    /**
     * Set questions_list
     * @param \Gein\Quiz\Api\Data\QuestionInterface[] $questionsList
     * @return \Gein\Quiz\Api\Data\TestingInterface
     */
    public function setQuestionsList($questionsList);

}
