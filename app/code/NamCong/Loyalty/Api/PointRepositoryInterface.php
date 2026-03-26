<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api;

use NamCong\Loyalty\Api\Data\PointsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PointRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(PointsInterface $points): PointsInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $pointId): PointsInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getByCustomerId(int $customerId): PointsInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(PointsInterface $points): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $pointId): bool;
}
