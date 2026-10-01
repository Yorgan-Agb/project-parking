<?php

declare(strict_types=1);

namespace App\UseCase\Stationnement;

use App\Domain\Entity\Stationnement;
use App\UseCase\Port\IClock;
use App\UseCase\Port\IIdGenerator;
use App\UseCase\Port\IReservationRepository;
use App\UseCase\Port\IStationnementRepository;

final class EnterParkingUseCase
{
    private readonly IReservationRepository $reservationRepository;
    private readonly IStationnementRepository $stationnementRepository;
    private readonly IIdGenerator $idGenerator;
    private readonly IClock $clock;

    public function __construct(
        IReservationRepository $reservationRepository,
        IStationnementRepository $stationnementRepository,
        IIdGenerator $idGenerator,
        IClock $clock
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->stationnementRepository = $stationnementRepository;
        $this->idGenerator = $idGenerator;
        $this->clock = $clock;
    }

    public function execute(EnterParkingRequest $request): EnterParkingResponse
    {
        $now = $this->clock->now();
        $userReservations = $this->reservationRepository->findByUserId($request->userId);

        $activeReservation = null;
        foreach ($userReservations as $reservation) {
            if ($reservation->parkingId === $request->parkingId && $reservation->isActiveAt($now)) {
                $activeReservation = $reservation;
                break;
            }
        }

        if ($activeReservation === null) {
            return EnterParkingResponse::failure('Aucune réservation active pour ce parking');
        }

        $userStationnements = $this->stationnementRepository->findByUserId($request->userId);

        $isAlreadyParked = false;
        foreach ($userStationnements as $stationnement) {
            if ($stationnement->parkingId === $request->parkingId && $stationnement->isOngoing()) {
                $isAlreadyParked = true;
                break;
            }
        }

        if ($isAlreadyParked) {
            return EnterParkingResponse::failure('Vous êtes déjà garé dans le parking');
        }

        $newStationnement = new Stationnement(
            $this->idGenerator->generate(),
            $request->userId,
            $request->parkingId,
            $activeReservation->id,
            $now,
        );

        $this->stationnementRepository->save($newStationnement);

        return EnterParkingResponse::success('Entrée autorisée');
    }
}
