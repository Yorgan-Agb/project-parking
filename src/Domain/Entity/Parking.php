<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\GpsCoordinates;
use App\Domain\ValueObject\OpeningHours;
use App\Domain\ValueObject\PricingGrid;

class Parking
{
    public readonly string $id;
    public readonly string $ownerId;
    public readonly string $name;
    public readonly GpsCoordinates $coordinates;
    public readonly int $totalSpots;
    public readonly PricingGrid $pricingGrid;
    public readonly OpeningHours $openingHours;

    public function __construct(string $id, string $ownerId, string $name, GpsCoordinates $coordinates, int $totalSpots, PricingGrid $pricingGrid, OpeningHours $openingHours)
    {
        if ($totalSpots < 1) {
            throw new \InvalidArgumentException('Un parking doit avoir au moins une place.');
        }

        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->name = $name;
        $this->coordinates = $coordinates;
        $this->totalSpots = $totalSpots;
        $this->pricingGrid = $pricingGrid;
        $this->openingHours = $openingHours;
    }

    public function isOpenDuring(\DateTimeImmutable $start, \DateTimeImmutable $end): bool
    {
        return $this->openingHours->isOpenDuring($start, $end);
    }

    public function priceFor(\DateTimeImmutable $start, \DateTimeImmutable $end): float
    {
        $minutes = (int) round(($end->getTimestamp() - $start->getTimestamp()) / 60);

        return $this->pricingGrid->priceForDuration($minutes);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ownerId' => $this->ownerId,
            'name' => $this->name,
            'coordinates' => $this->coordinates->toArray(),
            'totalSpots' => $this->totalSpots,
            'pricingGrid' => $this->pricingGrid->toArray(),
            'openingHours' => $this->openingHours->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['ownerId'],
            $data['name'],
            GpsCoordinates::fromArray($data['coordinates']),
            (int) $data['totalSpots'],
            PricingGrid::fromArray($data['pricingGrid']),
            OpeningHours::fromArray($data['openingHours']),
        );
    }
}
