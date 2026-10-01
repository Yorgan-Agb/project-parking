<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\InMemory;

use App\Domain\Entity\Parking;
use App\UseCase\Port\IParkingRepository;

final class InMemoryParkingRepository implements IParkingRepository
{
    /** @var Parking[] */
    private array $parkings;

    /**
     * @param Parking[] $parkings
     */
    public function __construct(array $parkings = [])
    {
        $this->parkings = array_values($parkings);
    }

    public function findById(string $id): ?Parking
    {
        foreach ($this->parkings as $parking) {
            if ($parking->id === $id) {
                return $parking;
            }
        }

        return null;
    }

    /**
     * @return Parking[]
     */
    public function findAll(): array
    {
        return $this->parkings;
    }

    /**
     * @return Parking[]
     */
    public function findByOwnerId(string $ownerId): array
    {
        return array_values(array_filter(
            $this->parkings,
            static fn(Parking $parking) => $parking->ownerId === $ownerId,
        ));
    }

    public function save(Parking $parking): void
    {
        foreach ($this->parkings as $index => $existingParking) {
            if ($existingParking->id === $parking->id) {
                $this->parkings[$index] = $parking;
                return;
            }
        }

        $this->parkings[] = $parking;
    }
}
