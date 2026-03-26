<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use NamCong\Loyalty\Api\PointManagerInterface;

class Redeem extends AbstractAccount implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly PointManagerInterface $pointManager
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $resultRedirect->setPath('customer/account/login');
        }

        $rewardId = (int) $this->getRequest()->getPostValue('reward_id');
        if (!$rewardId) {
            $this->messageManager->addErrorMessage(__('Invalid reward selected.'));
            return $resultRedirect->setPath('loyalty/account/index');
        }

        try {
            $this->pointManager->redeemReward($rewardId, (int) $this->customerSession->getCustomerId());
            $this->messageManager->addSuccessMessage(__('Reward redeemed successfully!'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
        }

        return $resultRedirect->setPath('loyalty/account/index');
    }
}
