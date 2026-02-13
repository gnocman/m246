<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface AnswerInterface
{

    const IS_CORRECT = 'is_correct';
    const QUESTION_ID = 'question_id';
    const QUESTION_TYPE = 'question_type';
    const ANSWER_ID = 'answer_id';
    const CONTENT = 'content';

    /**
     * Get answer_id
     * @return string|null
     */
    public function getAnswerId();

    /**
     * Set answer_id
     * @param string $answerId
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     */
    public function setAnswerId($answerId);

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId();

    /**
     * Set question_id
     * @param string $questionId
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     */
    public function setQuestionId($questionId);

    /**
     * Get question_type
     * @return string|null
     */
    public function getQuestionType();

    /**
     * Set question_type
     * @param string $questionType
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     */
    public function setQuestionType($questionType);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     */
    public function setContent($content);

    /**
     * Get is_correct
     * @return boolean|null
     */
    public function getIsCorrect();

    /**
     * Set is_correct
     * @param boolean $isCorrect
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     */
    public function setIsCorrect($isCorrect);
}

