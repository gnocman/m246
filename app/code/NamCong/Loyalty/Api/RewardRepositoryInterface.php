<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api;

use NamCong\Loyalty\Api\Data\RewardInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RewardRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(RewardInterface $reward): RewardInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $rewardId): RewardInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @return RewardInterface[]
     */
    public function getActiveRewards(): array;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(RewardInterface $reward): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $rewardId): bool;
}
