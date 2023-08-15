<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model;

use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    private array $loadedData = [];

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $accountsCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $accountsCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $accountsCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (!$this->loadedData) {
            foreach ($this->collection->getItems() as $accounts) {
                $this->loadedData[$accounts->getId()] = $accounts->getData();
            }
        }

        return $this->loadedData;
    }
}
