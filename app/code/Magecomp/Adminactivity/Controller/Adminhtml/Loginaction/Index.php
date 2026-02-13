<?php

namespace Magecomp\Adminactivity\Controller\Adminhtml\Loginaction;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Magecomp\Adminactivity\Controller\Adminhtml\Loginaction
 */
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
        $resultPage->setActiveMenu('Magecomp_Adminactivity::login_activity');
        $resultPage->addBreadcrumb(__('Magecomp'), __('Login Activity'));
        $resultPage->getConfig()->getTitle()->prepend(__('Login Activity'));

        return $resultPage;
    }
}
