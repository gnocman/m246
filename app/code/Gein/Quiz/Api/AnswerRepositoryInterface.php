<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api;

interface AnswerRepositoryInterface
{

    /**
     * Save Answer
     * @param \Gein\Quiz\Api\Data\AnswerInterface $answer
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Gein\Quiz\Api\Data\AnswerInterface $answer
    );

    /**
     * Save Answer
     * @param array $answerData
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load($answerData);

    /**
     * Retrieve Answer
     * @param string $answerId
     * @return \Gein\Quiz\Api\Data\AnswerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($answerId);

    /**
     * Retrieve Answer matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Gein\Quiz\Api\Data\AnswerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Answer
     * @param \Gein\Quiz\Api\Data\AnswerInterface $answer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Gein\Quiz\Api\Data\AnswerInterface $answer
    );

    /**
     * Delete Answer by ID
     * @param string $resultId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($answerId);

    /**
     * @param $questionId
     * @return \Gein\Quiz\Api\Data\AnswerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListAnswersByQuestionId($questionId);

    /**
     * @param $questionId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
     public function getListAnswerIdsByQuestionId($questionId);

    /**
     * @param $questionId
     * @param $questionType
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
     public function deleteAnswersByQuestionType($questionId, $questionType);
}

