<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\InMemory;

use App\Domain\Entity\Reservation;
use App\UseCase\Port\IReservationRepository;

final class InMemoryReservationRepository implements IReservationRepository
{
    /** @var Reservation[] */
    private array $reservations;

    /**
     * @param Reservation[] $reservations
     */
    public function __construct(array $reservations = [])
    {
        $this->reservations = array_values($reservations);
    }

    public function findById(string $id): ?Reservation
    {
        foreach ($this->reservations as $reservation) {
            if ($reservation->id === $id) {
                return $reservation;
            }
        }

        return null;
    }

    /**
     * @return Reservation[]
     */
    public function findByParkingId(string $parkingId): array
    {
        return array_values(array_filter(
            $this->reservations,
            static fn(Reservation $reservation) => $reservation->parkingId === $parkingId,
        ));
    }

    /**
     * @return Reservation[]
     */
    public function findByUserId(string $userId): array
    {
        return array_values(array_filter(
            $this->reservations,
            static fn(Reservation $reservation) => $reservation->userId === $userId,
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
        foreach ($this->reservations as $index => $existingReservation) {
            if ($existingReservation->id === $reservation->id) {
                $this->reservations[$index] = $reservation;
                return;
            }
        }

        $this->reservations[] = $reservation;
    }
}
