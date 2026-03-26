<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Reward;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rewards';

    public function __construct(
        Context $context,
        private readonly RewardRepositoryInterface $rewardRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $rewardId = (int) $this->getRequest()->getParam('reward_id');

        if (!$rewardId) {
            $this->messageManager->addErrorMessage(__('Invalid reward ID.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->rewardRepository->deleteById($rewardId);
            $this->messageManager->addSuccessMessage(__('The reward has been deleted.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Reward not found.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not delete the reward.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
