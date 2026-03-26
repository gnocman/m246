<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Rule;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use NamCong\Loyalty\Model\RuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rules';

    public function __construct(
        Context $context,
        private readonly RuleRepositoryInterface $ruleRepository,
        private readonly RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $ruleId = isset($data['rule_id']) ? (int) $data['rule_id'] : null;

        try {
            $rule = $ruleId
                ? $this->ruleRepository->getById($ruleId)
                : $this->ruleFactory->create();

            $rule->setName($data['name'] ?? '');
            $rule->setPoints((int) ($data['points'] ?? 0));
            $rule->setIsActive((bool) ($data['is_active'] ?? false));
            $rule->setFromDate($data['from_date'] ?? null);
            $rule->setToDate($data['to_date'] ?? null);
            $customerGroupIds = isset($data['customer_group_ids']) && is_array($data['customer_group_ids'])
                ? array_values(array_filter($data['customer_group_ids'], static fn ($groupId): bool => $groupId !== ''))
                : [];
            $rule->setCustomerGroupIds($customerGroupIds ? implode(',', $customerGroupIds) : null);

            $actionType = trim((string) ($data['action_type'] ?? HistoryInterface::ACTION_ORDER));
            $rule->setConditionSerialized(json_encode(['action_type' => $actionType], JSON_THROW_ON_ERROR));

            $this->ruleRepository->save($rule);
            $this->messageManager->addSuccessMessage(__('The loyalty rule has been saved.'));
            return $resultRedirect->setPath('*/*/edit', ['rule_id' => $rule->getRuleId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the rule.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['rule_id' => $ruleId]);
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
