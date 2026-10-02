<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class OpeningHours
{
    private readonly array $slots;

    /**
     * @param TimeSlot[] $slots
     */

    public function __construct(array $slots)
    {
        $this->slots = $slots;
    }

    public function isAlwaysOpen(): bool
    {
        return $this->slots === [];
    }

    public function isOpenAt(\DateTimeImmutable $instant): bool
    {
        if ($this->isAlwaysOpen()) {
            return true;
        }

        foreach ($this->slots as $slot) {
            if ($slot->contains($instant)) {
                return true;
            }
        }

        return false;
    }

    public function isOpenDuring(\DateTimeImmutable $start, \DateTimeImmutable $end): bool
    {
        if ($this->isAlwaysOpen()) {
            return true;
        }

        $cursor = $start;
        while ($cursor < $end) {
            if (!$this->isOpenAt($cursor)) {
                return false;
            }

            $cursor = $cursor->modify('+15 minutes');
        }

        return true;
    }

    public function toArray(): array
    {
        return array_map(
            static fn(TimeSlot $slot) => $slot->toArray(),
            $this->slots,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(array_map(
            static fn(array $slot) => TimeSlot::fromArray($slot),
            $data,
        ));
    }
}
