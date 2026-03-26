<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api\Data;

interface RewardInterface
{
    public const REWARD_ID       = 'reward_id';
    public const NAME            = 'name';
    public const REQUIRED_POINTS = 'required_points';
    public const REWARD_TYPE     = 'reward_type';
    public const REWARD_VALUE    = 'reward_value';
    public const IS_ACTIVE       = 'is_active';
    public const CREATED_AT      = 'created_at';
    public const UPDATED_AT      = 'updated_at';

    // Reward type constants
    public const TYPE_DISCOUNT      = 'discount';
    public const TYPE_FREE_SHIPPING = 'free_shipping';
    public const TYPE_GIFT_PRODUCT  = 'gift_product';

    public function getRewardId(): ?int;

    public function setRewardId(int $rewardId): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getRequiredPoints(): int;

    public function setRequiredPoints(int $points): self;

    public function getRewardType(): string;

    public function setRewardType(string $type): self;

    public function getRewardValue(): float;

    public function setRewardValue(float $value): self;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): self;

    public function getCreatedAt(): ?string;

    public function getUpdatedAt(): ?string;
}
