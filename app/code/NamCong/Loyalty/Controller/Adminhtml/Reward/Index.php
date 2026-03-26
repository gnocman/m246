<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Reward;

use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rewards';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\View\Result\Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NamCong_Loyalty::loyalty_rewards');
        $resultPage->addBreadcrumb(__('Loyalty Program'), __('Loyalty Program'));
        $resultPage->addBreadcrumb(__('Rewards Management'), __('Rewards Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rewards Management'));
        return $resultPage;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
