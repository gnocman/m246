<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Repository;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\HistoryRepositoryInterface;
use NamCong\Loyalty\Model\HistoryFactory;
use NamCong\Loyalty\Model\ResourceModel\History as HistoryResource;
use NamCong\Loyalty\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;

class HistoryRepository implements HistoryRepositoryInterface
{
    public function __construct(
        private readonly HistoryFactory $historyFactory,
        private readonly HistoryResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function save(HistoryInterface $history): HistoryInterface
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save loyalty history: %1', $e->getMessage()), $e);
        }
        return $history;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function getByCustomerId(int $customerId, int $limit = 20): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->setOrder('created_at', 'DESC');
        $collection->setPageSize($limit);
        return $collection->getItems();
    }
}
