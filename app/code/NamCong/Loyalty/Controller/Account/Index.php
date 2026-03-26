<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends AbstractAccount implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly CustomerSession $customerSession
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $isLoggedIn = $this->customerSession->isLoggedIn();

        if (!$isLoggedIn) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customer/account/login');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Loyalty Points'));
        return $resultPage;
    }
}
