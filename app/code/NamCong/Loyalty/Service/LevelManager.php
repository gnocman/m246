<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Service;

/**
 * Manages customer loyalty levels (Bronze/Silver/Gold gamification).
 */
class LevelManager
{
    public const LEVEL_BRONZE = 'bronze';
    public const LEVEL_SILVER = 'silver';
    public const LEVEL_GOLD   = 'gold';

    public const SILVER_THRESHOLD = 500;
    public const GOLD_THRESHOLD   = 2000;

    public const LEVELS = [
        self::LEVEL_BRONZE => 0,
        self::LEVEL_SILVER => self::SILVER_THRESHOLD,
        self::LEVEL_GOLD   => self::GOLD_THRESHOLD,
    ];

    public function calculateLevel(int $totalPoints): string
    {
        return match (true) {
            $totalPoints >= self::GOLD_THRESHOLD   => self::LEVEL_GOLD,
            $totalPoints >= self::SILVER_THRESHOLD => self::LEVEL_SILVER,
            default                                => self::LEVEL_BRONZE,
        };
    }

    public function getNextLevelThreshold(string $currentLevel): ?int
    {
        return match ($currentLevel) {
            self::LEVEL_BRONZE => self::SILVER_THRESHOLD,
            self::LEVEL_SILVER => self::GOLD_THRESHOLD,
            default            => null, // Gold is the top level
        };
    }

    public function getProgressPercentage(int $totalPoints): float
    {
        $level = $this->calculateLevel($totalPoints);
        $nextThreshold = $this->getNextLevelThreshold($level);

        if ($nextThreshold === null) {
            return 100.0;
        }

        $currentThreshold = self::LEVELS[$level];
        $rangePoints = $nextThreshold - $currentThreshold;
        $earnedPoints = $totalPoints - $currentThreshold;

        return round(min(($earnedPoints / $rangePoints) * 100, 100), 2);
    }

    public function getLevelLabel(string $level): string
    {
        return match ($level) {
            self::LEVEL_GOLD   => __('Gold')->render(),
            self::LEVEL_SILVER => __('Silver')->render(),
            default            => __('Bronze')->render(),
        };
    }
}
