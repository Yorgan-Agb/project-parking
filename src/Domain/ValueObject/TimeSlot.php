<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class TimeSlot
{
    public readonly int $dayOfWeek;
    public readonly string $startTime;
    public readonly string $endTime;

    public function __construct(int $dayOfWeek, string $startTime, string $endTime)
    {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new \InvalidArgumentException('Day of week must be between 1 (Monday) and 7 (Sunday)');
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $startTime) || !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            throw new \InvalidArgumentException('Start time and end time must be in HH:MM format');
        }

        $this->dayOfWeek = $dayOfWeek;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function contains(\DateTimeImmutable $instant): bool
    {
        $day = (int) $instant->format('N');
        $time = $instant->format('H:i');

        if ($this->startTime < $this->endTime) {
            return $day === $this->dayOfWeek && $time >= $this->startTime && $time < $this->endTime;
        }

        if ($this->dayOfWeek === 7) {
            $nextDay = 1;
        } else {
            $nextDay = $this->dayOfWeek + 1;
        }
        return ($day === $this->dayOfWeek && $time >= $this->startTime) || ($day === $nextDay && $time < $this->endTime);
    }

    public function toArray(): array
    {
        return ['dayOfWeek' => $this->dayOfWeek, 'startTime' => $this->startTime, 'endTime' => $this->endTime];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['dayOfWeek'],
            (string) $data['startTime'],
            (string) $data['endTime'],
        );
    }
}
