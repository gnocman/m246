<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api\Data;

interface PointsInterface
{
    public const POINT_ID      = 'point_id';
    public const CUSTOMER_ID   = 'customer_id';
    public const TOTAL_POINTS  = 'total_points';
    public const LEVEL         = 'level';
    public const CREATED_AT    = 'created_at';
    public const UPDATED_AT    = 'updated_at';

    public function getPointId(): ?int;

    public function setPointId(int $pointId): self;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): self;

    public function getTotalPoints(): int;

    public function setTotalPoints(int $points): self;

    public function getLevel(): string;

    public function setLevel(string $level): self;

    public function getCreatedAt(): ?string;

    public function getUpdatedAt(): ?string;
}
