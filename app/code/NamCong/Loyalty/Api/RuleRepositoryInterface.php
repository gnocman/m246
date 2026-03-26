<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api;

use NamCong\Loyalty\Api\Data\RuleInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RuleRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(RuleInterface $rule): RuleInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $ruleId): RuleInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @return RuleInterface[]
     */
    public function getActiveRules(): array;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(RuleInterface $rule): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $ruleId): bool;
}
