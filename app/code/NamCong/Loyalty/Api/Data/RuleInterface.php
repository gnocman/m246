<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api\Data;

interface RuleInterface
{
    public const RULE_ID               = 'rule_id';
    public const NAME                  = 'name';
    public const POINTS                = 'points';
    public const CONDITION_SERIALIZED  = 'condition_serialized';
    public const CUSTOMER_GROUP_IDS    = 'customer_group_ids';
    public const IS_ACTIVE             = 'is_active';
    public const FROM_DATE             = 'from_date';
    public const TO_DATE               = 'to_date';
    public const CREATED_AT            = 'created_at';
    public const UPDATED_AT            = 'updated_at';

    public function getRuleId(): ?int;

    public function setRuleId(int $ruleId): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getPoints(): int;

    public function setPoints(int $points): self;

    public function getConditionSerialized(): ?string;

    public function setConditionSerialized(?string $condition): self;

    public function getCustomerGroupIds(): ?string;

    public function setCustomerGroupIds(?string $groupIds): self;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): self;

    public function getFromDate(): ?string;

    public function setFromDate(?string $fromDate): self;

    public function getToDate(): ?string;

    public function setToDate(?string $toDate): self;

    public function getCreatedAt(): ?string;

    public function getUpdatedAt(): ?string;
}
