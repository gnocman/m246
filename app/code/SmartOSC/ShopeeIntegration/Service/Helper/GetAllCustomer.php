<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Service\Helper;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResourceConnection;

class GetAllCustomer
{
    /**
     * @var Curl
     */
    private Curl $curlClient;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param Curl $curlClient
     * @param ManagerInterface $messageManager
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Curl $curlClient,
        ManagerInterface $messageManager,
        ResourceConnection $resourceConnection
    ) {
        $this->curlClient = $curlClient;
        $this->messageManager = $messageManager;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get All Information of Customer Integration
     *
     * @return array|mixed
     */
    public function getAllCustomers()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('shopee_integration_accounts');

        // Get base_url and access_token from the database
        $select = $connection->select()
            ->from($tableName, ['base_url', 'access_token'])
            ->limit(1); // You might need to adjust this depending on your use case

        $data = $connection->fetchRow($select);

        if (!$data) {
            $this->messageManager->addErrorMessage(__('No integration data found.'));
            return [];
        }

        $baseUrl = $data['base_url'];
        $accessToken = $data['access_token'];

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
            $this->messageManager->addErrorMessage(__('Error Message'));
            return [];
        }
    }
}
