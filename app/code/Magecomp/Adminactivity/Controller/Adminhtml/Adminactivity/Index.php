<?php

namespace Magecomp\Adminactivity\Controller\Adminhtml\Adminactivity;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends Action
{


    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magecomp_Adminactivity::activity');
        $resultPage->addBreadcrumb(__('Magecomp'), __('Admin Activity'));
        $resultPage->getConfig()->getTitle()->prepend(__('Admin Activity'));

        return $resultPage;
    }
}
