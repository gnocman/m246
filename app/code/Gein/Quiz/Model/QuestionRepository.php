<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\QuestionInterface;
use Gein\Quiz\Api\Data\QuestionInterfaceFactory;
use Gein\Quiz\Api\Data\QuestionSearchResultsInterfaceFactory;
use Gein\Quiz\Api\QuestionRepositoryInterface;
use Gein\Quiz\Model\ResourceModel\Question as ResourceQuestion;
use Gein\Quiz\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class QuestionRepository implements QuestionRepositoryInterface
{

    /**
     * @var QuestionCollectionFactory
     */
    protected $questionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ResourceQuestion
     */
    protected $resource;

    /**
     * @var QuestionInterfaceFactory
     */
    protected $questionFactory;

    /**
     * @var Question
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceQuestion $resource
     * @param QuestionInterfaceFactory $questionFactory
     * @param QuestionCollectionFactory $questionCollectionFactory
     * @param QuestionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceQuestion                      $resource,
        QuestionInterfaceFactory              $questionFactory,
        QuestionCollectionFactory             $questionCollectionFactory,
        QuestionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface          $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->questionFactory = $questionFactory;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(QuestionInterface $question)
    {
        try {
            $this->resource->save($question);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the question: %1',
                $exception->getMessage()
            ));
        }
        return $question;
    }

    /**
     * @inheritDoc
     */
    public function get($questionId)
    {
        $question = $this->questionFactory->create();
        $this->resource->load($question, $questionId);
        if (!$question->getId()) {
            throw new NoSuchEntityException(__('Question with id "%1" does not exist.', $questionId));
        }
        return $question;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->questionCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(QuestionInterface $question)
    {
        try {
            $questionModel = $this->questionFactory->create();
            $this->resource->load($questionModel, $question->getQuestionId());
            $this->resource->delete($questionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Question: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($questionId)
    {
        return $this->delete($this->get($questionId));
    }

    /**
     * @inheritDoc
     */
    public function getExamQuestions($examId)
    {
        try {
            $questionCollection = $this->questionCollectionFactory->create();
            $questionCollection->addFieldToSelect('question_id')
                ->addFieldToFilter('exam_id', $examId);
            return $questionCollection->getColumnValues('question_id');
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Exam with id "%1" does not exist.', $examId));
        }

    }

    /**
     * @inheritDoc
     */
    public function updateExamQuestions($examId, $questionIds)
    {
        try {
            $questionCollection = $this->questionCollectionFactory->create();
            $currentExamQuestions = $questionCollection->addFieldToFilter('exam_id', $examId);
            $currentQuestionIds = $currentExamQuestions->getColumnValues('question_id');
            if ($currentQuestionIds == $questionIds) {
                return true;
            }
            $oldExamQuestionIds = array_diff($currentQuestionIds, $questionIds);
            if (!empty($oldExamQuestionIds)) {
                foreach ($oldExamQuestionIds as $questionId) {
                    $question = $this->questionFactory->create();
                    $this->resource->load($question, $questionId);
                    $question->setExamId(null);
                    $question->setUpdateTime(date("Y-m-d H:i:s"));
                    $this->resource->save($question);
                }
            }
            $newExamQuestionIds = array_diff($questionIds, $currentQuestionIds);
            if (!empty($newExamQuestionIds)) {
                foreach ($newExamQuestionIds as $questionId) {
                    $question = $this->questionFactory->create();
                    $this->resource->load($question, $questionId);
                    $question->setExamId($examId);
                    $question->setUpdateTime(date("Y-m-d H:i:s"));
                    $this->resource->save($question);
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception(__('Update exam questions failed.'));
        }

    }
}

