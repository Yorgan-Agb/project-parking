<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Reservation
{
    public readonly string $id;
    public readonly string $userId;
    public readonly string $parkingId;
    public readonly \DateTimeImmutable $start;
    public readonly \DateTimeImmutable $end;
    public readonly float $price;

    public function __construct(string $id, string $userId, string $parkingId, \DateTimeImmutable $start, \DateTimeImmutable $end, float $price)
    {
        if ($end <= $start) {
            throw new \InvalidArgumentException('End time must be after start time');
        }

        $this->id = $id;
        $this->userId = $userId;
        $this->parkingId = $parkingId;
        $this->start = $start;
        $this->end = $end;
        $this->price = $price;
    }

    public function isActiveAt(\DateTimeImmutable $instant): bool
    {
        return $this->start <= $instant && $instant < $this->end;
    }

    public function overlaps(\DateTimeImmutable $start, \DateTimeImmutable $end): bool
    {
        return $this->start < $end && $start < $this->end;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'parkingId' => $this->parkingId,
            'start' => $this->start->format(DATE_ATOM),
            'end' => $this->end->format(DATE_ATOM),
            'price' => $this->price,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['userId'],
            $data['parkingId'],
            new \DateTimeImmutable($data['start']),
            new \DateTimeImmutable($data['end']),
            (float) $data['price'],
        );
    }
}
