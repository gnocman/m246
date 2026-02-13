<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Controller\Adminhtml\Exam;

class Edit extends \Gein\Quiz\Controller\Adminhtml\Exam
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\Registry                $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('exam_id');
        $model = $this->_objectManager->create(\Gein\Quiz\Model\Exam::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Exam no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('gein_quiz_exam', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Exam') : __('New Exam'),
            $id ? __('Edit Exam') : __('New Exam')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Exams'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Exam %1', $model->getId()) : __('New Exam'));
        return $resultPage;
    }
}

