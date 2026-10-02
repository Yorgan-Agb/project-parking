<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Json;

use App\Domain\Entity\Parking;
use App\Infrastructure\Storage\JsonFileStore;
use App\UseCase\Port\IParkingRepository;

final class JsonParkingRepository implements IParkingRepository
{
    private readonly JsonFileStore $store;

    public function __construct(string $filePath)
    {
        $this->store = new JsonFileStore($filePath);
    }

    public function findById(string $id): ?Parking
    {
        foreach ($this->store->readAll() as $row) {
            if ($row['id'] === $id) {
                return Parking::fromArray($row);
            }
        }

        return null;
    }

    /**
     * @return Parking[]
     */
    public function findAll(): array
    {
        return array_values(array_map(
            static fn(array $row) => Parking::fromArray($row),
            $this->store->readAll(),
        ));
    }

    /**
     * @return Parking[]
     */
    public function findByOwnerId(string $ownerId): array
    {
        $rows = array_filter(
            $this->store->readAll(),
            static fn(array $row) => $row['ownerId'] === $ownerId,
        );

        return array_values(array_map(
            static fn(array $row) => Parking::fromArray($row),
            $rows,
        ));
    }

    public function save(Parking $parking): void
    {
        $this->store->upsert($parking->toArray());
    }
}
