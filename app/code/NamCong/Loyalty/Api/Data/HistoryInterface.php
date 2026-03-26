<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api\Data;

interface HistoryInterface
{
    public const HISTORY_ID  = 'history_id';
    public const CUSTOMER_ID = 'customer_id';
    public const POINTS      = 'points';
    public const ACTION_TYPE = 'action_type';
    public const ORDER_ID    = 'order_id';
    public const COMMENT     = 'comment';
    public const CREATED_AT  = 'created_at';

    // Action type constants
    public const ACTION_ORDER        = 'order';
    public const ACTION_REGISTRATION = 'registration';
    public const ACTION_REVIEW       = 'review';
    public const ACTION_REDEMPTION   = 'redemption';
    public const ACTION_MANUAL       = 'manual';
    public const ACTION_EXPIRATION   = 'expiration';
    public const ACTION_DEDUCTION    = 'deduction';

    public function getHistoryId(): ?int;

    public function setHistoryId(int $historyId): self;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): self;

    public function getPoints(): int;

    public function setPoints(int $points): self;

    public function getActionType(): string;

    public function setActionType(string $actionType): self;

    public function getOrderId(): ?int;

    public function setOrderId(?int $orderId): self;

    public function getComment(): ?string;

    public function setComment(?string $comment): self;

    public function getCreatedAt(): ?string;
}
