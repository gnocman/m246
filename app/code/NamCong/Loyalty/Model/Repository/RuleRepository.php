<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Repository;

use NamCong\Loyalty\Api\Data\RuleInterface;
use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Model\RuleFactory;
use NamCong\Loyalty\Model\ResourceModel\Rule as RuleResource;
use NamCong\Loyalty\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RuleRepository implements RuleRepositoryInterface
{
    public function __construct(
        private readonly RuleFactory $ruleFactory,
        private readonly RuleResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function save(RuleInterface $rule): RuleInterface
    {
        try {
            $this->resource->save($rule);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save loyalty rule: %1', $e->getMessage()), $e);
        }
        return $rule;
    }

    public function getById(int $ruleId): RuleInterface
    {
        $rule = $this->ruleFactory->create();
        $this->resource->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Loyalty rule with id "%1" not found.', $ruleId));
        }
        return $rule;
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

    public function getActiveRules(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $today = date('Y-m-d');
        $collection->addFieldToFilter(
            ['from_date', 'from_date'],
            [
                ['null' => true],
                ['lteq' => $today]
            ]
        );
        $collection->addFieldToFilter(
            ['to_date', 'to_date'],
            [
                ['null' => true],
                ['gteq' => $today]
            ]
        );
        return $collection->getItems();
    }

    public function delete(RuleInterface $rule): bool
    {
        try {
            $this->resource->delete($rule);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete loyalty rule: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $ruleId): bool
    {
        return $this->delete($this->getById($ruleId));
    }
}
