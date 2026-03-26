<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model;

use NamCong\Loyalty\Api\Data\RewardInterface;
use Magento\Framework\Model\AbstractModel;
use NamCong\Loyalty\Model\ResourceModel\Reward as RewardResource;

class Reward extends AbstractModel implements RewardInterface
{
    protected $_eventPrefix = 'namcong_loyalty_reward';

    protected function _construct(): void
    {
        $this->_init(RewardResource::class);
    }

    public function getRewardId(): ?int
    {
        $id = $this->getData(self::REWARD_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setRewardId(int $rewardId): self
    {
        return $this->setData(self::REWARD_ID, $rewardId);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getRequiredPoints(): int
    {
        return (int) $this->getData(self::REQUIRED_POINTS);
    }

    public function setRequiredPoints(int $points): self
    {
        return $this->setData(self::REQUIRED_POINTS, $points);
    }

    public function getRewardType(): string
    {
        return (string) ($this->getData(self::REWARD_TYPE) ?? self::TYPE_DISCOUNT);
    }

    public function setRewardType(string $type): self
    {
        return $this->setData(self::REWARD_TYPE, $type);
    }

    public function getRewardValue(): float
    {
        return (float) $this->getData(self::REWARD_VALUE);
    }

    public function setRewardValue(float $value): self
    {
        return $this->setData(self::REWARD_VALUE, $value);
    }

    public function getIsActive(): bool
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): self
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
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
