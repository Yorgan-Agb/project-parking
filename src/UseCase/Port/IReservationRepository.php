<?php

declare(strict_types=1);

namespace App\UseCase\Port;

use App\Domain\Entity\Reservation;

interface IReservationRepository
{
    public function findById(string $id): ?Reservation;

    /** @return Reservation[] */
    public function findByParkingId(string $parkingId): array;

    /** @return Reservation[] */
    public function findByUserId(string $userId): array;

    /** @return Reservation[] */
    public function findOverlapping(string $parkingId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;

    public function save(Reservation $reservation): void;
}
