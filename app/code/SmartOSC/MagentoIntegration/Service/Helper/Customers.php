<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Service\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SmartOSC\MagentoIntegration\Service\Client\Curl;

class Customers
{
    private const ENABLE = 'integration_customers/general/enable';
    private const BASE_URL = 'integration_customers/general/base_url';
    private const ACCESS_TOKEN = 'integration_customers/general/access_token';

    /**
     * @var Curl
     */
    private Curl $curlClient;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param Curl $curlClient
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Curl $curlClient,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->curlClient = $curlClient;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get All Information of Customer Integration
     *
     * @return array|mixed
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

                return $responseData['items'];
            } else {
                return [];
            }
        }

        return [];
    }

    /**
     * Create New Customer
     *
     * @param $customerData
     * @return array|mixed
     */
    public function createCustomer($customerData)
    {
        $enable = $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);

        if ($enable) {
            $baseUrl = $this->scopeConfig->getValue(self::BASE_URL, ScopeInterface::SCOPE_STORE);
            $accessToken = $this->scopeConfig->getValue(self::ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

            $url = "$baseUrl/rest/V1/customers/";
            $headers = [
                "Authorization: Bearer $accessToken",
                "Content-Type" => "application/json"
            ];

            $this->curlClient->setHeaders($headers);
            $this->curlClient->post($url, json_encode($customerData));

            $responseBody = $this->curlClient->getBody();
            $httpStatus = $this->curlClient->getStatus();

            if ($httpStatus === 200) {
                return json_decode($responseBody, true);
            } else {
                return [];
            }
        }

        return [];
    }

    /**
     * Delete Customer
     *
     * @param $customerId
     * @return array|mixed
     */
    public function deleteCustomer($customerId)
    {
        $enable = $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);

        if ($enable) {
            $baseUrl = $this->scopeConfig->getValue(self::BASE_URL, ScopeInterface::SCOPE_STORE);
            $accessToken = $this->scopeConfig->getValue(self::ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

            $url = "$baseUrl/rest/V1/customers/" . $customerId;
            $headers = [
                "Authorization: Bearer $accessToken",
                "Content-Type" => "application/json"
            ];

            $this->curlClient->setHeaders($headers);
            $this->curlClient->delete($url);

            $responseBody = $this->curlClient->getBody();
            $httpStatus = $this->curlClient->getStatus();

            if ($httpStatus === 200) {
                return json_decode($responseBody, true);
            } else {
                return [];
            }
        }

        return [];
    }

    /**
     * Edit Customer
     *
     * @param $customerId
     * @param $customerData
     * @return array|mixed
     */
    public function editCustomer($customerId, $customerData)
    {
        $enable = $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);

        if ($enable) {
            $baseUrl = $this->scopeConfig->getValue(self::BASE_URL, ScopeInterface::SCOPE_STORE);
            $accessToken = $this->scopeConfig->getValue(self::ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

            $url = "$baseUrl/rest/V1/customers/" . $customerId;
            $headers = [
                "Authorization: Bearer $accessToken",
                "Content-Type" => "application/json"
            ];

            $this->curlClient->setHeaders($headers);
            $this->curlClient->put($url, $customerData);

            $responseBody = $this->curlClient->getBody();
            $httpStatus = $this->curlClient->getStatus();

            if ($httpStatus === 200) {
                return json_decode($responseBody, true);
            } else {
                return [];
            }
        }

        return [];
    }
}
