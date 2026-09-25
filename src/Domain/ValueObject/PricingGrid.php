<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class PricingGrid
{
    private const MINUTES_PER_SLICE = 15;
    private readonly array $tiers;

    /**
     * @param PricingTier[] $tiers
     */

    public function __construct(array $tiers)
    {
        if (count($tiers) === 0) {
            throw new \InvalidArgumentException('Une grille tarifaire doit contenir au moins un palier.');
        }

        usort($tiers, static fn(PricingTier $a, PricingTier $b) => $a->fromMinute <=> $b->fromMinute);

        if ($tiers[0]->fromMinute !== 0) {
            throw new \InvalidArgumentException('Le premier palier tarifaire doit démarrer à la minute 0.');
        }

        $this->tiers = $tiers;
    }

    public function priceForDuration(int $durationInMinutes): float
    {
        $sliceCount = (int) ceil($durationInMinutes / self::MINUTES_PER_SLICE);
        $total = 0.0;

        for ($i = 0; $i < $sliceCount; $i++) {
            $elapsedAtSliceStart = $i * self::MINUTES_PER_SLICE;
            $total += $this->tierApplicableAt($elapsedAtSliceStart)->pricePerQuarterHour;
        }

        return $total;
    }

    private function tierApplicableAt(int $elapsedMinutes): PricingTier
    {
        $applicable = $this->tiers[0];
        foreach ($this->tiers as $tier) {
            if ($tier->fromMinute <= $elapsedMinutes) {
                $applicable = $tier;
            }
        }

        return $applicable;
    }
}
