<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Model;

use Gein\Quiz\Api\Data\QuestionInterface;
use Magento\Framework\Model\AbstractModel;

class Question extends AbstractModel implements QuestionInterface
{

    /**
     * @var \Gein\Quiz\Api\Data\AnswerInterfaceFactory
     */
    private $_answerFactory;

    /**
     * @var \Gein\Quiz\Model\ResourceModel\Answer\CollectionFactory
     */
    private $_resultCollectionFactory;

    /**
     * @param \Gein\Quiz\Api\Data\AnswerInterfaceFactory $answerFactory
     * @param \Gein\Quiz\Model\ResourceModel\Answer\CollectionFactory $resultCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Gein\Quiz\Api\Data\AnswerInterfaceFactory              $answerFactory,
        \Gein\Quiz\Model\ResourceModel\Answer\CollectionFactory $resultCollectionFactory,
        \Magento\Framework\Model\Context                        $context,
        \Magento\Framework\Registry                             $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection = null,
        array                                                   $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_answerFactory = $answerFactory;
        $this->_resultCollectionFactory = $resultCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Gein\Quiz\Model\ResourceModel\Question::class);
    }

    /**
     * @inheritDoc
     */
    public function getQuestionId()
    {
        return $this->getData(self::QUESTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionId($questionId)
    {
        return $this->setData(self::QUESTION_ID, $questionId);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getExamId()
    {
        return $this->getData(self::EXAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExamId($examId)
    {
        return $this->setData(self::EXAM_ID, $examId);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getCreateTime()
    {
        return $this->getData(self::CREATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setCreateTime($createTime)
    {
        return $this->setData(self::CREATE_TIME, $createTime);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @inheritDoc
     */
    public function getResultsList()
    {
        return $this->getData(self::RESULTS_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setResultsList($resultsList)
    {
        return $this->setData(self::RESULTS_LIST, $resultsList);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionData($questionData)
    {
        $this->setData($questionData);
        $answersList = [];
        $answersData = $this->_resultCollectionFactory->create()
            ->addFieldToFilter('main_table.question_id', $this->getQuestionId())->getData();
        if ($answersData) {
            foreach ($answersData as $answer) {
                $answersList[] = $this->_answerFactory->create()->setData($answer);
            }
        }
        $this->setResultsList($answersList);
    }
}

