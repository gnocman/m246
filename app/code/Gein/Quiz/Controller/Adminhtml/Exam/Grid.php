<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gein\Quiz\Controller\Adminhtml\Exam;

use Gein\Quiz\Model\ExamFactory;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var ExamFactory
     */
    protected $examFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param ExamFactory $examFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        ExamFactory $examFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->examFactory = $examFactory;
    }

    /**
     * Grid Action
     * Display list of questions related to current exam
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $exam = $this->_initExam();
        if (!$exam) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('quiz/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Gein\Quiz\Block\Adminhtml\Exam\Tab\Question::class,
                'exam.question.grid'
            )->toHtml()
        );
    }

    /**
     * Initialize requested exam
     *
     * @return \Gein\Quiz\Model\Exam
     */
    protected function _initExam()
    {
        $examId = (int)$this->getRequest()->getParam('exam_id', false);
        $exam = $this->examFactory->create();

        if ($examId) {
            $exam->load($examId);
        }
        return $exam;
    }
}
