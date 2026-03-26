<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml\Reward;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Controller\Adminhtml\AbstractLoyaltyAction;
use NamCong\Loyalty\Model\RewardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends AbstractLoyaltyAction
{
    public const ADMIN_RESOURCE = 'NamCong_Loyalty::manage_rewards';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly RewardRepositoryInterface $rewardRepository,
        private readonly RewardFactory $rewardFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $rewardId = (int) $this->getRequest()->getParam('reward_id');

        if ($rewardId) {
            try {
                $this->rewardRepository->getById($rewardId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This reward no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NamCong_Loyalty::loyalty_rewards');
        $title = $rewardId ? __('Edit Reward') : __('New Reward');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
