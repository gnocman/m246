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

class Orders
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
     * Get All Information of Order Integration
     *
     * @param $fromDate
     * @param $toDate
     * @return array|mixed
     */
    public function getAllOrders($fromDate, $toDate)
    {
        $enable = $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);

        if ($enable) {
            $baseUrl = $this->scopeConfig->getValue(self::BASE_URL, ScopeInterface::SCOPE_STORE);
            $accessToken = $this->scopeConfig->getValue(self::ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

            $url = "$baseUrl/rest/V1/orders?searchCriteria[filter_groups][1][filters][0][field]=created_at&searchCriteria[filter_groups][1][filters][0][value]=$fromDate&searchCriteria[filter_groups][1][filters][0][condition_type]=from&searchCriteria[filter_groups][2][filters][0][field]=created_at&searchCriteria[filter_groups][2][filters][0][value]=$toDate&searchCriteria[filter_groups][2][filters][0][condition_type]=to";
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
}
