<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\AnswerInterface;
use Gein\Quiz\Api\Data\AnswerInterfaceFactory;
use Gein\Quiz\Api\Data\AnswerSearchResultsInterfaceFactory;
use Gein\Quiz\Api\AnswerRepositoryInterface;
use Gein\Quiz\Model\ResourceModel\Answer as ResourceResult;
use Gein\Quiz\Model\ResourceModel\Answer\CollectionFactory as AnswerCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AnswerRepository implements AnswerRepositoryInterface
{

    /**
     * @var ResourceResult
     */
    protected $resource;

    /**
     * @var AnswerInterfaceFactory
     */
    protected $answerFactory;

    /**
     * @var Answer
     */
    protected $searchResultsFactory;

    /**
     * @var AnswerCollectionFactory
     */
    protected $answerCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ResourceResult $resource
     * @param AnswerInterfaceFactory $resultFactory
     * @param AnswerCollectionFactory $answerCollectionFactory
     * @param AnswerSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceResult                      $resource,
        AnswerInterfaceFactory              $resultFactory,
        AnswerCollectionFactory             $answerCollectionFactory,
        AnswerSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder               $searchCriteriaBuilder,
        CollectionProcessorInterface        $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->answerFactory = $resultFactory;
        $this->answerCollectionFactory = $answerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(AnswerInterface $answer)
    {
        try {
            $this->resource->save($answer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the answer: %1',
                $exception->getMessage()
            ));
        }
        return $answer;
    }

    /**
     * @inheritDoc
     */
    public function load($answerData)
    {
        if (isset($answerData['answer_id']) && !empty($answerData['answer_id'])) {
            $answer = $this->answerFactory->create();
            $this->resource->load($answer, $answerData['answer_id']);
        } else {
            unset($answerData['answer_id']);
            $answer = $this->answerFactory->create();
        }
        $answer->setData($answerData);
        return $answer;
    }

    /**
     * @inheritDoc
     */
    public function get($answerId)
    {
        $answer = $this->answerFactory->create();
        $this->resource->load($answer, $answerId);
        if (!$answer->getId()) {
            throw new NoSuchEntityException(__('Answer with id "%1" does not exist.', $answerId));
        }
        return $answer;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->answerCollectionFactory->create();

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
    public function delete(AnswerInterface $answer)
    {
        try {
            $answerModel = $this->answerFactory->create();
            $this->resource->load($answerModel, $answer->getAnswerId());
            $this->resource->delete($answerModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Answer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($answerId)
    {
        return $this->delete($this->get($answerId));
    }

    /**
     * @inheritDoc
     */
    public function getListAnswersByQuestionId($questionId)
    {
        $searchCriteria =  $this->searchCriteriaBuilder->addFilter(
            AnswerInterface::QUESTION_ID,
            $questionId
        )->create();
        return $this->getList($searchCriteria)->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getListAnswerIdsByQuestionId($questionId)
    {
        $collection = $this->answerCollectionFactory->create();
        $answerIds = $collection->addFieldToSelect('answer_id')
        ->addFieldToFilter('main_table.question_id', $questionId)
        ->getColumnValues('answer_id');

        return $answerIds;
    }

    /**
     * @inheritDoc
     */
    public function deleteAnswersByQuestionType($questionId, $questionType)
    {
        $collection = $this->answerCollectionFactory->create();
        $answers = $collection->addFieldToSelect('answer_id')
        ->addFieldToFilter('main_table.question_id', $questionId)
        ->addFieldToFilter('main_table.question_type', ['neq' => $questionType])
        ->getItems();
        if (!empty($answers)) {
            foreach ($answers as $answer) {
                $this->delete($answer);
            }
            return true;
        }
        return false;
    }
}

