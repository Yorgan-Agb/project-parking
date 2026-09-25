<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Stationnement
{
    public readonly string $id;
    public readonly string $userId;
    public readonly string $parkingId;
    public readonly string $reservationId;
    public readonly \DateTimeImmutable $entry;
    public readonly ?\DateTimeImmutable $exit;

    public function __construct(string $id, string $userId, string $parkingId, string $reservationId, \DateTimeImmutable $entry, ?\DateTimeImmutable $exit = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->parkingId = $parkingId;
        $this->reservationId = $reservationId;
        $this->entry = $entry;
        $this->exit = $exit;
    }

    public function isOngoing(): bool
    {
        return $this->exit === null;
    }

    public function withExit(\DateTimeImmutable $exit): self
    {
        return new self(
            $this->id,
            $this->userId,
            $this->parkingId,
            $this->reservationId,
            $this->entry,
            $exit,
        );
    }
}
