<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ExamRepositoryInterface
{

    /**
     * Save Exam
     * @param \Gein\Quiz\Api\Data\ExamInterface $exam
     * @return \Gein\Quiz\Api\Data\ExamInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Gein\Quiz\Api\Data\ExamInterface $exam);

    /**
     * Retrieve Exam
     * @param string $examId
     * @return \Gein\Quiz\Api\Data\ExamInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($examId);

    /**
     * Retrieve Exam matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Gein\Quiz\Api\Data\ExamSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Exam
     * @param \Gein\Quiz\Api\Data\ExamInterface $exam
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Gein\Quiz\Api\Data\ExamInterface $exam);

    /**
     * Delete Exam by ID
     * @param string $examId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($examId);

    /**
     * Retrieve Exam matching the specified criteria.
     * @param int $examId
     * @return \Gein\Quiz\Api\Data\TestingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTesting($examId);
}

