<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Json;

use App\Domain\Entity\Stationnement;
use App\Infrastructure\Storage\JsonFileStore;
use App\UseCase\Port\IStationnementRepository;

final class JsonStationnementRepository implements IStationnementRepository
{
    private readonly JsonFileStore $store;

    public function __construct(string $filePath)
    {
        $this->store = new JsonFileStore($filePath);
    }

    /**
     * @return Stationnement[]
     */
    public function findByUserId(string $userId): array
    {
        $rows = array_filter(
            $this->store->readAll(),
            static fn(array $row) => $row['userId'] === $userId,
        );

        return array_values(array_map(
            static fn(array $row) => Stationnement::fromArray($row),
            $rows,
        ));
    }

    public function save(Stationnement $stationnement): void
    {
        $this->store->upsert($stationnement->toArray());
    }
}
