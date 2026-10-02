<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Json;

use App\Domain\Entity\Reservation;
use App\Infrastructure\Storage\JsonFileStore;
use App\UseCase\Port\IReservationRepository;

final class JsonReservationRepository implements IReservationRepository
{
    private readonly JsonFileStore $store;

    public function __construct(string $filePath)
    {
        $this->store = new JsonFileStore($filePath);
    }

    public function findById(string $id): ?Reservation
    {
        foreach ($this->store->readAll() as $row) {
            if ($row['id'] === $id) {
                return Reservation::fromArray($row);
            }
        }

        return null;
    }

    /**
     * @return Reservation[]
     */
    public function findByParkingId(string $parkingId): array
    {
        $rows = array_filter(
            $this->store->readAll(),
            static fn(array $row) => $row['parkingId'] === $parkingId,
        );

        return array_values(array_map(
            static fn(array $row) => Reservation::fromArray($row),
            $rows,
        ));
    }

    /**
     * @return Reservation[]
     */
    public function findByUserId(string $userId): array
    {
        $rows = array_filter(
            $this->store->readAll(),
            static fn(array $row) => $row['userId'] === $userId,
        );

        return array_values(array_map(
            static fn(array $row) => Reservation::fromArray($row),
            $rows,
        ));
    }

    /**
     * @return Reservation[]
     */
    public function findOverlapping(string $parkingId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        return array_values(array_filter(
            $this->findByParkingId($parkingId),
            static fn(Reservation $reservation) => $reservation->overlaps($start, $end),
        ));
    }

    public function save(Reservation $reservation): void
    {
        $this->store->upsert($reservation->toArray());
    }
}
