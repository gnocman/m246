<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Repository;

use NamCong\Loyalty\Api\Data\PointsInterface;
use NamCong\Loyalty\Api\PointRepositoryInterface;
use NamCong\Loyalty\Model\PointsFactory;
use NamCong\Loyalty\Model\ResourceModel\Points as PointsResource;
use NamCong\Loyalty\Model\ResourceModel\Points\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PointRepository implements PointRepositoryInterface
{
    public function __construct(
        private readonly PointsFactory $pointsFactory,
        private readonly PointsResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function save(PointsInterface $points): PointsInterface
    {
        try {
            $this->resource->save($points);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save loyalty points: %1', $e->getMessage()), $e);
        }
        return $points;
    }

    public function getById(int $pointId): PointsInterface
    {
        $points = $this->pointsFactory->create();
        $this->resource->load($points, $pointId);
        if (!$points->getId()) {
            throw new NoSuchEntityException(__('Loyalty points with id "%1" not found.', $pointId));
        }
        return $points;
    }

    public function getByCustomerId(int $customerId): PointsInterface
    {
        $points = $this->pointsFactory->create();
        $this->resource->loadByCustomerId($points, $customerId);
        if (!$points->getId()) {
            // Auto-create record for new customers
            $points->setCustomerId($customerId);
            $points->setTotalPoints(0);
            $points->setLevel('bronze');
            $this->save($points);
        }
        return $points;
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

    public function delete(PointsInterface $points): bool
    {
        try {
            $this->resource->delete($points);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete loyalty points: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $pointId): bool
    {
        return $this->delete($this->getById($pointId));
    }
}
