<?php

declare(strict_types=1);

namespace App\UseCase\Reservation;

final class CreateReservationRequest
{
    public readonly string $userId;
    public readonly string $parkingId;
    public readonly \DateTimeImmutable $start;
    public readonly \DateTimeImmutable $end;
    public function __construct(string $userId, string $parkingId, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $this->userId = $userId;
        $this->parkingId = $parkingId;
        $this->start = $start;
        $this->end = $end;
    }
}
