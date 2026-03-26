<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Ui\DataProvider\Reward;

use NamCong\Loyalty\Model\ResourceModel\Reward\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class FormDataProvider extends AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }

        $rewardId = (int) $this->request->getParam('reward_id');
        if ($rewardId) {
            $this->collection->addFieldToFilter('reward_id', $rewardId);
            foreach ($this->collection->getItems() as $reward) {
                // Return wrapped in 'data' key or without depending on dataScope config
                $this->loadedData[$reward->getId()] = $reward->getData();
            }
        }

        // Must always return an array, even empty
        return $this->loadedData;
    }
}
