<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Controller\Adminhtml\Question;

use Gein\Common\Constants\Quiz;
use Gein\Quiz\Api\AnswerRepositoryInterface;
use Gein\Quiz\Model\ImageUploader;
use Gein\Quiz\Model\QuestionFactory;
use Gein\Quiz\Ui\DataProvider\Question\Form\Modifier\AnswerOptions;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;


class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var QuestionFactory
     */
    protected $questionFactory;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var Manager
     */
    protected $cacheManager;
    /**
     * @var ImageUploader
     */
    protected $imageUploaderModel;

    /**
     * @var AnswerRepositoryInterface
     */
    protected $resultRepository;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param QuestionFactory $questionFactory
     * @param ManagerInterface $messageManager
     * @param Manager $cacheManager
     * @param ImageUploader $imageUploaderModel
     * @param AnswerRepositoryInterface $resultRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context                   $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        PageFactory                                           $resultPageFactory,
        QuestionFactory                                       $questionFactory,
        ImageUploader                                         $imageUploaderModel,
        ManagerInterface                                      $messageManager,
        Manager                                               $cacheManager,
        AnswerRepositoryInterface                             $resultRepository
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->resultPageFactory = $resultPageFactory;
        $this->questionFactory = $questionFactory;
        $this->imageUploaderModel = $imageUploaderModel;
        $this->messageManager = $messageManager;
        $this->cacheManager = $cacheManager;
        $this->resultRepository = $resultRepository;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('question_id');

            $model = $this->_objectManager->create(\Gein\Quiz\Model\Question::class)->load($id);
            // validate data
            $errorFlag = false;
            $message = null;
            if (!$model->getId() && $id) {
                $errorFlag = true;
                $message = __('This Question no longer exists.');
                return $resultRedirect->setPath('*/*/');
            }

            $questionType = $data[AnswerOptions::DATA_SOURCE_DEFAULT][AnswerOptions::FIELD_QUESTION_TYPE_NAME];
            $answersData = $data[AnswerOptions::DATA_SOURCE_DEFAULT][AnswerOptions::GRID_OPTIONS_NAME] ?? [];
            $answersData = $questionType == Quiz::ESSAY ? $data[AnswerOptions::DATA_SOURCE_DEFAULT] : $answersData;
            if (empty($answersData)) {
                $errorFlag = true;
                $message = __('This Question has not any answer.');
            } else {
                $numberOfCorrectAnswers = count(array_keys(array_column($answersData, 'is_correct'), 1));
                if ($questionType == Quiz::SINGLE_CHOICE && $numberOfCorrectAnswers != 1) {
                    $errorFlag = true;
                    $message = __('You can only choose an answer for single-choice questions.');
                } elseif ($questionType == Quiz::MULTIPLE_CHOICE && $numberOfCorrectAnswers < 2) {
                    $errorFlag = true;
                    $message = __('You must select 2 or more answers for multiple-choice questions.');
                }
            }
            if ($errorFlag) {
                $this->messageManager->addErrorMessage($message);
                $this->dataPersistor->set('gein_quiz_question', $data);
                return $resultRedirect->setPath('*/*/edit', ['question_id' => $this->getRequest()->getParam('question_id')]);
            }
            $model->setData($data);
            $connection = $this->questionFactory->create()->getResource()->getConnection();
            try {
                $connection->beginTransaction();
                $model->setType($questionType);
                if (isset($data['question_image'])) {
                    $model = $this->setImageData($model, $data);
                }
                $model->save();
                //delete answers have other question type
                $this->resultRepository->deleteAnswersByQuestionType($model->getId(), $questionType);
                // save the answer options
                if ($questionType == Quiz::ESSAY) {
                    $answerModel = $this->resultRepository->load($answersData);
                    $answerModel->setQuestionId($model->getId());
                    $answerModel->setQuestionType($questionType);
                    $this->resultRepository->save($answerModel);
                } else {
                    $oldAnswers = $this->resultRepository->getListAnswerIdsByQuestionId($model->getId());
                    $newAnswerIds = array_column($answersData, 'answer_id');
                    $removedAnswerIds = array_diff($oldAnswers, $newAnswerIds);
                    if (!empty($removedAnswerIds)) {
                        foreach ($removedAnswerIds as $removedAnswerId) {
                            $this->resultRepository->deleteById($removedAnswerId);
                        }
                    }
                    if ($answersData) {
                        foreach ($answersData as $answerData) {
                            $answerModel = $this->resultRepository->load($answerData);
                            $answerModel->setData($answerData);
                            $answerModel->setQuestionId($model->getId());
                            $answerModel->setQuestionType($questionType);
                            $this->resultRepository->save($answerModel);
                        }
                    }
                }
                $connection->commit();
                $this->messageManager->addSuccessMessage(__('You saved the Question.'));
                $this->dataPersistor->clear('gein_quiz_question');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['question_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $connection->rollBack();
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $connection->rollBack();
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Question.'));
            }

            $this->dataPersistor->set('gein_quiz_question', $data);
            return $resultRedirect->setPath('*/*/edit', ['question_id' => $this->getRequest()->getParam('question_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $model
     * @param $data
     * @return mixed
     */
    public function setImageData($model, $data)
    {
        if ($model->getId()) {
            $pageData = $this->questionFactory->create();
            $pageData->load($model->getId());
            if (isset($data['question_image'][0]['name'])) {
                $imageName1 = $pageData->getThumbnail();
                $imageName2 = $data['question_image'][0]['name'];
                if ($imageName1 != $imageName2) {
                    $imageUrl = $data['question_image'][0]['url'];
                    $imageName = $data['question_image'][0]['name'];
                    $data['question_image'] = $this->imageUploaderModel->saveMediaImage($imageName, $imageUrl);
                } else {
                    $data['question_image'] = $data['question_image'][0]['name'];
                }
            } else {
                $data['question_image'] = '';
            }
        } else {
            if (isset($data['question_image'][0]['name'])) {
                $imageUrl = $data['question_image'][0]['url'];
                $imageName = $data['question_image'][0]['name'];
                $data['question_image'] = $this->imageUploaderModel->saveMediaImage($imageName, $imageUrl);
            }
        }
        $model->setImage($data['question_image']);
        return $model;
    }
}

