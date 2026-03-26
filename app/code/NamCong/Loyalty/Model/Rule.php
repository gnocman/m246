<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model;

use NamCong\Loyalty\Api\Data\RuleInterface;
use Magento\Framework\Model\AbstractModel;
use NamCong\Loyalty\Model\ResourceModel\Rule as RuleResource;

class Rule extends AbstractModel implements RuleInterface
{
    protected $_eventPrefix = 'namcong_loyalty_rule';

    protected function _construct(): void
    {
        $this->_init(RuleResource::class);
    }

    public function getRuleId(): ?int
    {
        $id = $this->getData(self::RULE_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setRuleId(int $ruleId): self
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getPoints(): int
    {
        return (int) $this->getData(self::POINTS);
    }

    public function setPoints(int $points): self
    {
        return $this->setData(self::POINTS, $points);
    }

    public function getConditionSerialized(): ?string
    {
        return $this->getData(self::CONDITION_SERIALIZED);
    }

    public function setConditionSerialized(?string $condition): self
    {
        return $this->setData(self::CONDITION_SERIALIZED, $condition);
    }

    public function getCustomerGroupIds(): ?string
    {
        return $this->getData(self::CUSTOMER_GROUP_IDS);
    }

    public function setCustomerGroupIds(?string $groupIds): self
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, $groupIds);
    }

    public function getIsActive(): bool
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): self
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getFromDate(): ?string
    {
        return $this->getData(self::FROM_DATE);
    }

    public function setFromDate(?string $fromDate): self
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    public function getToDate(): ?string
    {
        return $this->getData(self::TO_DATE);
    }

    public function setToDate(?string $toDate): self
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }
}
