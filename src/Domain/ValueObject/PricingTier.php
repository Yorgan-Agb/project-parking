<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class PricingTier
{
    public readonly int $fromMinute;
    public readonly float $pricePerQuarterHour;

    public function __construct(int $fromMinute, float $pricePerQuarterHour)
    {
        if ($fromMinute < 0 || $pricePerQuarterHour < 0) {
            throw new \InvalidArgumentException('Price per quarter hour and from minute must be non-negative');
        }
        $this->fromMinute = $fromMinute;
        $this->pricePerQuarterHour = $pricePerQuarterHour;
    }

    public function toArray(): array
    {
        return ['fromMinute' => $this->fromMinute, 'pricePerQuarterHour' => $this->pricePerQuarterHour];
    }

    public static function fromArray(array $data): self
    {
        return new self((int) $data['fromMinute'], (float) $data['pricePerQuarterHour']);
    }
}
