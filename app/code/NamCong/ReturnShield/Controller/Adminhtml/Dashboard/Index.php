<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'NamCong_ReturnShield::dashboard';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NamCong_ReturnShield::risk_dashboard');
        $resultPage->addBreadcrumb(__('ReturnShield'), __('ReturnShield'));
        $resultPage->addBreadcrumb(__('Return Risk Dashboard'), __('Return Risk Dashboard'));
        $resultPage->getConfig()->getTitle()->prepend(__('Return Risk Dashboard'));

        return $resultPage;
    }
}
