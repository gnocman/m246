<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;
use NamCong\Loyalty\Model\ResourceModel\History as HistoryResource;

class History extends AbstractModel implements HistoryInterface
{
    protected $_eventPrefix = 'namcong_loyalty_history';

    protected function _construct(): void
    {
        $this->_init(HistoryResource::class);
    }

    public function getHistoryId(): ?int
    {
        $id = $this->getData(self::HISTORY_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setHistoryId(int $historyId): self
    {
        return $this->setData(self::HISTORY_ID, $historyId);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): self
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getPoints(): int
    {
        return (int) $this->getData(self::POINTS);
    }

    public function setPoints(int $points): self
    {
        return $this->setData(self::POINTS, $points);
    }

    public function getActionType(): string
    {
        return (string) $this->getData(self::ACTION_TYPE);
    }

    public function setActionType(string $actionType): self
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    public function getOrderId(): ?int
    {
        $id = $this->getData(self::ORDER_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setOrderId(?int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getComment(): ?string
    {
        return $this->getData(self::COMMENT);
    }

    public function setComment(?string $comment): self
    {
        return $this->setData(self::COMMENT, $comment);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }
}
