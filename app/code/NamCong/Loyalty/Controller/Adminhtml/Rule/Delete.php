<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Rule;

use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rules';

    public function __construct(
        Context $context,
        private readonly RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ruleId = (int) $this->getRequest()->getParam('rule_id');

        if (!$ruleId) {
            $this->messageManager->addErrorMessage(__('Invalid rule ID.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->ruleRepository->deleteById($ruleId);
            $this->messageManager->addSuccessMessage(__('The loyalty rule has been deleted.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Rule not found.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not delete the rule.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
