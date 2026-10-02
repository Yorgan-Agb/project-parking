<?php

declare(strict_types=1);

namespace App\UseCase\Parking;

final class ParkingSummaryResponse
{
    public readonly string $id;
    public readonly string $name;
    public readonly float $longitude;
    public readonly float $latitude;
    public readonly int $totalSpots;

    public function __construct(string $id, string $name, float $longitude, float $latitude, int $totalSpots)
    {
        $this->id = $id;
        $this->name = $name;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->totalSpots = $totalSpots;
    }
}
