<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Reward;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use NamCong\Loyalty\Model\RewardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rewards';

    public function __construct(
        Context $context,
        private readonly RewardRepositoryInterface $rewardRepository,
        private readonly RewardFactory $rewardFactory
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

        $rewardId = isset($data['reward_id']) ? (int) $data['reward_id'] : null;

        try {
            $reward = $rewardId
                ? $this->rewardRepository->getById($rewardId)
                : $this->rewardFactory->create();

            $reward->setName($data['name'] ?? '');
            $reward->setRequiredPoints((int) ($data['required_points'] ?? 0));
            $reward->setRewardType($data['reward_type'] ?? 'discount');
            $reward->setRewardValue((float) ($data['reward_value'] ?? 0.0));
            $reward->setIsActive((bool) ($data['is_active'] ?? false));

            $this->rewardRepository->save($reward);
            $this->messageManager->addSuccessMessage(__('The reward has been saved.'));
            return $resultRedirect->setPath('*/*/edit', ['reward_id' => $reward->getRewardId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the reward.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['reward_id' => $rewardId]);
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
