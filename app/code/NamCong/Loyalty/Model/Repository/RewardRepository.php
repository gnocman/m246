<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Repository;

use NamCong\Loyalty\Api\Data\RewardInterface;
use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Model\RewardFactory;
use NamCong\Loyalty\Model\ResourceModel\Reward as RewardResource;
use NamCong\Loyalty\Model\ResourceModel\Reward\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RewardRepository implements RewardRepositoryInterface
{
    public function __construct(
        private readonly RewardFactory $rewardFactory,
        private readonly RewardResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function save(RewardInterface $reward): RewardInterface
    {
        try {
            $this->resource->save($reward);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save loyalty reward: %1', $e->getMessage()), $e);
        }
        return $reward;
    }

    public function getById(int $rewardId): RewardInterface
    {
        $reward = $this->rewardFactory->create();
        $this->resource->load($reward, $rewardId);
        if (!$reward->getId()) {
            throw new NoSuchEntityException(__('Loyalty reward with id "%1" not found.', $rewardId));
        }
        return $reward;
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

    public function getActiveRewards(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setOrder('required_points', 'ASC');
        return $collection->getItems();
    }

    public function delete(RewardInterface $reward): bool
    {
        try {
            $this->resource->delete($reward);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete loyalty reward: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $rewardId): bool
    {
        return $this->delete($this->getById($rewardId));
    }
}
