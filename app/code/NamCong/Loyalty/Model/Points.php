<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model;

use NamCong\Loyalty\Api\Data\PointsInterface;
use Magento\Framework\Model\AbstractModel;
use NamCong\Loyalty\Model\ResourceModel\Points as PointsResource;

class Points extends AbstractModel implements PointsInterface
{
    protected $_eventPrefix = 'namcong_loyalty_points';

    protected function _construct(): void
    {
        $this->_init(PointsResource::class);
    }

    public function getPointId(): ?int
    {
        $id = $this->getData(self::POINT_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setPointId(int $pointId): self
    {
        return $this->setData(self::POINT_ID, $pointId);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): self
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getTotalPoints(): int
    {
        return (int) $this->getData(self::TOTAL_POINTS);
    }

    public function setTotalPoints(int $points): self
    {
        return $this->setData(self::TOTAL_POINTS, $points);
    }

    public function getLevel(): string
    {
        return (string) ($this->getData(self::LEVEL) ?? 'bronze');
    }

    public function setLevel(string $level): self
    {
        return $this->setData(self::LEVEL, $level);
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
