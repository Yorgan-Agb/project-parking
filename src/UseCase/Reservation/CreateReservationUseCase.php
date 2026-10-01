<?php

declare(strict_types=1);

namespace App\UseCase\Reservation;

use App\Domain\Entity\Reservation;
use App\UseCase\Port\IClock;
use App\UseCase\Port\IIdGenerator;
use App\UseCase\Port\IParkingRepository;
use App\UseCase\Port\IReservationRepository;

final class CreateReservationUseCase
{
    private readonly IParkingRepository $parkingRepository;
    private readonly IReservationRepository $reservationRepository;
    private readonly IIdGenerator $idGenerator;
    private readonly IClock $clock;

    public function __construct(
        IParkingRepository $parkingRepository,
        IReservationRepository $reservationRepository,
        IIdGenerator $idGenerator,
        IClock $clock
    ) {
        $this->parkingRepository = $parkingRepository;
        $this->reservationRepository = $reservationRepository;
        $this->idGenerator = $idGenerator;
        $this->clock = $clock;
    }

    public function execute(CreateReservationRequest $request): CreateReservationResponse
    {
        if ($request->end <= $request->start) {
            return CreateReservationResponse::failure(
                'INVALID_RANGE',
                'La fin de la réservation doit être postérieure au début.',
            );
        }

        if ($request->start < $this->clock->now()) {
            return CreateReservationResponse::failure(
                'PAST_RESERVATION',
                'Impossible de réserver un créneau déjà passé.',
            );
        }

        $parking = $this->parkingRepository->findById($request->parkingId);
        if ($parking === null) {
            return CreateReservationResponse::failure('PARKING_NOT_FOUND', 'Ce parking n\'existe pas.');
        }

        if (!$parking->isOpenDuring($request->start, $request->end)) {
            return CreateReservationResponse::failure(
                'PARKING_CLOSED',
                'Le parking est fermé sur une partie du créneau demandé.',
            );
        }

        if (!$this->hasCapacityDuring($parking->id, $parking->totalSpots, $request->start, $request->end)) {
            return CreateReservationResponse::failure(
                'NO_AVAILABILITY',
                'Le parking est complet sur une partie du créneau demandé.',
            );
        }

        $price = $parking->priceFor($request->start, $request->end);

        $reservation = new Reservation(
            $this->idGenerator->generate(),
            $request->userId,
            $request->parkingId,
            $request->start,
            $request->end,
            $price,
        );

        $this->reservationRepository->save($reservation);

        return CreateReservationResponse::success(
            $reservation->id,
            $reservation->parkingId,
            $reservation->start->format(DATE_ATOM),
            $reservation->end->format(DATE_ATOM),
            $reservation->price,
        );
    }

    private function hasCapacityDuring(
        string $parkingId,
        int $totalSpots,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
    ): bool {
        $overlapping = $this->reservationRepository->findOverlapping($parkingId, $start, $end);

        $cursor = $start;
        while ($cursor < $end) {
            $occupied = 0;
            foreach ($overlapping as $reservation) {
                if ($reservation->isActiveAt($cursor)) {
                    $occupied++;
                }
            }

            if ($occupied >= $totalSpots) {
                return false;
            }

            $cursor = $cursor->modify('+15 minutes');
        }

        return true;
    }
}
