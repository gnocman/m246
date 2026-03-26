<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Ui\DataProvider\Rule;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
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
        private readonly Json $serializer,
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

        $ruleId = (int) $this->request->getParam('rule_id');
        if ($ruleId) {
            $this->collection->addFieldToFilter('rule_id', $ruleId);
            foreach ($this->collection->getItems() as $rule) {
                $data = $rule->getData();
                if (!empty($data['customer_group_ids'])) {
                    $data['customer_group_ids'] = explode(',', $data['customer_group_ids']);
                }

                $data['action_type'] = HistoryInterface::ACTION_ORDER;
                if (!empty($data['condition_serialized'])) {
                    try {
                        $conditions = $this->serializer->unserialize($data['condition_serialized']);
                        $data['action_type'] = $conditions['action_type'] ?? HistoryInterface::ACTION_ORDER;
                    } catch (\InvalidArgumentException) {
                        $data['action_type'] = HistoryInterface::ACTION_ORDER;
                    }
                }

                $this->loadedData[$rule->getId()] = $data;
            }
        }

        return $this->loadedData;
    }
}
