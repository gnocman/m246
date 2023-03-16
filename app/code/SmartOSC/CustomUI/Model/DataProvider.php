<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CustomUI\Model;

use SmartOSC\CustomUI\Model\ResourceModel\Film\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Get Data Provider
 */
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
     * @param CollectionFactory $filmCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $filmCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $filmCollectionFactory->create();
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
            foreach ($this->collection->getItems() as $film) {
                $this->loadedData[$film->getId()] = $film->getData();
            }
        }

        return $this->loadedData;
    }
}
