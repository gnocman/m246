<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\ExamInterface;
use Gein\Quiz\Api\Data\ExamInterfaceFactory;
use Gein\Quiz\Api\Data\ExamSearchResultsInterfaceFactory;
use Gein\Quiz\Api\Data\QuestionInterfaceFactory;
use Gein\Quiz\Api\Data\TestingInterfaceFactory;
use Gein\Quiz\Api\ExamRepositoryInterface;
use Gein\Quiz\Model\ResourceModel\Exam as ResourceExam;
use Gein\Quiz\Model\ResourceModel\Exam\CollectionFactory as ExamCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExamRepository implements ExamRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ExamCollectionFactory
     */
    protected $examCollectionFactory;

    /**
     * @var ExamInterfaceFactory
     */
    protected $examFactory;

    /**
     * @var TestingInterfaceFactory
     */
    protected $testingFactory;

    /**
     * @var QuestionInterfaceFactory
     */
    protected $questionInterfaceFactory;

    /**
     * @var Exam
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceExam
     */
    protected $resource;


    /**
     * @param ResourceExam $resource
     * @param ExamInterfaceFactory $examFactory
     * @param TestingInterfaceFactory $testingFactory
     * @param QuestionInterfaceFactory $questionInterfaceFactory
     * @param ExamCollectionFactory $examCollectionFactory
     * @param ExamSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceExam                      $resource,
        ExamInterfaceFactory              $examFactory,
        TestingInterfaceFactory           $testingFactory,
        QuestionInterfaceFactory          $questionInterfaceFactory,
        ExamCollectionFactory             $examCollectionFactory,
        ExamSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface      $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->examFactory = $examFactory;
        $this->testingFactory = $testingFactory;
        $this->questionInterfaceFactory = $questionInterfaceFactory;
        $this->examCollectionFactory = $examCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(ExamInterface $exam)
    {
        try {
            $this->resource->save($exam);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the exam: %1',
                $exception->getMessage()
            ));
        }
        return $exam;
    }

    /**
     * @inheritDoc
     */
    public function get($examId)
    {
        $exam = $this->examFactory->create();
        $this->resource->load($exam, $examId);
        if (!$exam->getId()) {
            throw new NoSuchEntityException(__('Exam with id "%1" does not exist.', $examId));
        }
        return $exam;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->examCollectionFactory->create();

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
    public function delete(ExamInterface $exam)
    {
        try {
            $examModel = $this->examFactory->create();
            $this->resource->load($examModel, $exam->getExamId());
            $this->resource->delete($examModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Exam: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($examId)
    {
        return $this->delete($this->get($examId));
    }

    /**
     * @inheritDoc
     */
    public function getTesting($examId)
    {
        $testing = $this->testingFactory->create();
        $examCollection = $this->examCollectionFactory->create();
        $testingData = $examCollection->getQuestionsByExamId($examId)->getData();
        if (!$testingData) {
            throw new NoSuchEntityException(__('Exam with id "%1" does not exist.', $examId));
        }
        $testing->setName($testingData[0]['name']);
        $testing->setDescription($testingData[0]['description']);
        $testing->setTestingDate($testingData[0]['testing_date']);
        $testing->setTotalTime($testingData[0]['total_time']);
        $testing->setStatus($testingData[0]['status']);
        $questionsList = [];
        foreach ($testingData as $questionData) {
            $question = $this->questionInterfaceFactory->create();
            $question->setQuestionData($questionData);
            $questionsList[] = $question;
        }
        $testing->setQuestionsList($questionsList);
        return $testing;
    }
}

