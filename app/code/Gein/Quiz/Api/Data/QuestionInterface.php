<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface QuestionInterface
{

    const TYPE = 'type';
    const QUESTION_ID = 'question_id';
    const UPDATE_TIME = 'update_time';
    const CONTENT = 'content';
    const CREATE_TIME = 'create_time';
    const IMAGE = 'image';
    const EXAM_ID = 'exam_id';
    const RESULTS_LIST = 'results_list';

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId();

    /**
     * Set question_id
     * @param string $questionId
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setQuestionId($questionId);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setContent($content);

    /**
     * Get exam_id
     * @return string|null
     */
    public function getExamId();

    /**
     * Set exam_id
     * @param string $examId
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setExamId($examId);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setType($type);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setImage($image);

    /**
     * Get create_time
     * @return string|null
     */
    public function getCreateTime();

    /**
     * Set create_time
     * @param string $createTime
     * @return \Gein\Quiz\Api\Data\QuestionInterface
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
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Get results_list
     * @return \Gein\Quiz\Api\Data\AnswerInterface[]|null
     */
    public function getResultsList();

    /**
     * Set results_list
     * @param \Gein\Quiz\Api\Data\AnswerInterface[] $resultsList
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setResultsList($resultsList);

    /**
     * Set question data
     * @param array $questionData
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setQuestionData($questionData);
}

