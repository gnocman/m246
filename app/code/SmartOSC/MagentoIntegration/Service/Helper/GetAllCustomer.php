<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Service\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use SmartOSC\MagentoIntegration\Model\CustomersFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers as CustomerResource;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers\CollectionFactory;

class GetAllCustomer
{
    private const ENABLE = 'integration_customers/general/enable';
    private const BASE_URL = 'integration_customers/general/base_url';
    private const ACCESS_TOKEN = 'integration_customers/general/access_token';

    /**
     * @var Curl
     */
    private Curl $curlClient;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var CustomersFactory
     */
    private CustomersFactory $customersFactory;
    /**
     * @var CustomerResource
     */
    private CustomerResource $customersResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $CustomersCollectionFactory;

    /**
     * @param Curl $curlClient
     * @param ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomersFactory $customersFactory
     * @param CustomerResource $customersResource
     * @param CollectionFactory $CustomersCollectionFactory
     */
    public function __construct(
        Curl $curlClient,
        ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        CustomersFactory $customersFactory,
        CustomerResource $customersResource,
        CollectionFactory $CustomersCollectionFactory
    ) {
        $this->curlClient = $curlClient;
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->customersFactory = $customersFactory;
        $this->customersResource = $customersResource;
        $this->CustomersCollectionFactory = $CustomersCollectionFactory;
    }

    /**
     * Get All Information of Customer Integration
     *
     * @return array
     * @throws AlreadyExistsException
     */
    public function getAllCustomers()
    {
        $enable = $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);

        if ($enable) {
            $baseUrl = $this->scopeConfig->getValue(self::BASE_URL, ScopeInterface::SCOPE_STORE);
            $accessToken = $this->scopeConfig->getValue(self::ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

            $url = "$baseUrl/rest/V1/customers/search?searchCriteria[pageSize]=100";
            $headers = [
                "Authorization: Bearer $accessToken"
            ];

            $this->curlClient->setHeaders($headers);
            $this->curlClient->get($url);

            $responseBody = $this->curlClient->getBody();
            $httpStatus = $this->curlClient->getStatus();

            if ($httpStatus === 200) {
                $responseData = json_decode($responseBody, true);

                $customers = $responseData['items'];

                foreach ($customers as $customer) {
                    $existingCustomer = $this->CustomersCollectionFactory->create()
                        ->addFieldToFilter('email', $customer['email'])
                        ->getFirstItem();

                    if (!$existingCustomer->getId()) {
                        $data = $this->customersFactory->create();
                        $data->addData([
                            "customer_id" => $customer['id'],
                            "email" => $customer['email'],
                            "firstname" => $customer['firstname'],
                            "lastname" => $customer['lastname'],
                            "created_in" => $customer['created_in'],
                        ]);
                        $this->customersResource->save($data);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Error Message'));

                return [];
            }
        }

        return [];
    }
}
