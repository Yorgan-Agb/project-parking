<?php

declare(strict_types=1);

namespace App\UseCase\Stationnement;

final class ExitParkingRequest
{
    public readonly string $userId;
    public readonly string $parkingId;

    public function __construct(string $userId, string $parkingId)
    {
        $this->userId = $userId;
        $this->parkingId = $parkingId;
    }
}
