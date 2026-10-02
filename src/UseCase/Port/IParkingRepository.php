<?php

declare(strict_types=1);

namespace App\UseCase\Port;

use App\Domain\Entity\Parking;

interface IParkingRepository
{
    public function findById(string $id): ?Parking;

    /** @return Parking[] */
    public function findAll(): array;

    /** @return Parking[] */
    public function findByOwnerId(string $ownerId): array;

    public function save(Parking $parking): void;
}
