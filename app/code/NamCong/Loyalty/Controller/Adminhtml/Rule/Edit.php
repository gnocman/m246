<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Rule;

use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use NamCong\Loyalty\Model\RuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rules';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly RuleRepositoryInterface $ruleRepository,
        private readonly RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $ruleId = (int) $this->getRequest()->getParam('rule_id');

        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->getById($ruleId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NamCong_Loyalty::loyalty_rules');
        $title = $ruleId ? __('Edit Loyalty Rule') : __('New Loyalty Rule');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
