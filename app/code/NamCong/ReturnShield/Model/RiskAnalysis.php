<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Model;

class RiskAnalysis
{
    /**
     * @param string[] $reasons
     * @param string[] $recommendations
     */
    public function __construct(
        private readonly int $score,
        private readonly string $label,
        private readonly array $reasons,
        private readonly array $recommendations
    ) {
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string[]
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }

    /**
     * @return string[]
     */
    public function getRecommendations(): array
    {
        return $this->recommendations;
    }
}
