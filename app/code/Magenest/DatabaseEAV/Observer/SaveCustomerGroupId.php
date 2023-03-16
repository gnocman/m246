<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 *
 */
class SaveCustomerGroupId implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    public CustomerRepositoryInterface $_customerRepositoryInterface;
    /**
     * @var ManagerInterface
     */
    public ManagerInterface $_messageManager;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        ManagerInterface $messageManager
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $accountController = $observer->getAccountController();
        $request = $accountController->getRequest();
        $group_id = $request->getParam('group_id');

        try {
            $customerId = $observer->getCustomer()->getId();
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $customer->setGroupId($group_id);
            $this->_customerRepositoryInterface->save($customer);

        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage(__('Something went wrong! Please try again.'));
        }
    }
}
