<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Controller\Adminhtml\Exam;

use Gein\Quiz\Api\QuestionRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param QuestionRepositoryInterface $questionRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context                   $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        QuestionRepositoryInterface                           $questionRepository
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->questionRepository = $questionRepository;
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
            $id = $this->getRequest()->getParam('exam_id');

            $model = $this->_objectManager->create(\Gein\Quiz\Model\Exam::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Exam no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $testingDate = str_replace('/', '-', $data['testing_date']);
            $data['testing_date'] = date('Y-m-d', (int)strtotime($testingDate));
            if ($id) {
                $data['update_time'] = date("Y-m-d H:i:s");
            }
            $model->setData($data);

            try {
                $model->save();
                // update assign questions
                $questions = empty($data['exam_questions']) ? [] : explode(',', $data['exam_questions']);
                $this->questionRepository->updateExamQuestions($id, $questions);

                $this->messageManager->addSuccessMessage(__('You saved the Exam.'));
                $this->dataPersistor->clear('gein_quiz_exam');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['exam_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Exam.'));
            }

            $this->dataPersistor->set('gein_quiz_exam', $data);
            return $resultRedirect->setPath('*/*/edit', ['exam_id' => $this->getRequest()->getParam('exam_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

