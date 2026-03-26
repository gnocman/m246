<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;

interface HistoryRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(HistoryInterface $history): HistoryInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @return HistoryInterface[]
     */
    public function getByCustomerId(int $customerId, int $limit = 20): array;
}
