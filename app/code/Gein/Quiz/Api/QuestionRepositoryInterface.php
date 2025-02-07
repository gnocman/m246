<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QuestionRepositoryInterface
{

    /**
     * Save Question
     * @param \Gein\Quiz\Api\Data\QuestionInterface $question
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Gein\Quiz\Api\Data\QuestionInterface $question
    );

    /**
     * Retrieve Question
     * @param string $questionId
     * @return \Gein\Quiz\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($questionId);

    /**
     * Retrieve Question matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Gein\Quiz\Api\Data\QuestionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Question
     * @param \Gein\Quiz\Api\Data\QuestionInterface $question
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Gein\Quiz\Api\Data\QuestionInterface $question
    );

    /**
     * Delete Question by ID
     * @param string $questionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($questionId);

    /**
     * @param $examId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExamQuestions($examId);

    /**
     * @param $examId
     * @param $questionIds
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateExamQuestions($examId, $questionIds);
}

