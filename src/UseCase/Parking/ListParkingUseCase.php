<?php

declare(strict_types=1);

namespace App\UseCase\Parking;

use App\Domain\Entity\Parking;
use App\UseCase\Port\IListParkingPresenter;
use App\UseCase\Port\IParkingRepository;

final class ListParkingUseCase
{
    private readonly IParkingRepository $parkingRepository;

    public function __construct(IParkingRepository $parkingRepository)
    {
        $this->parkingRepository = $parkingRepository;
    }

    public function execute(IListParkingPresenter $presenter): void
    {
        $allParkings = $this->parkingRepository->findAll();

        $parkingSummaries = array_map(
            static fn(Parking $parking) => new ParkingSummaryResponse(
                $parking->id,
                $parking->name,
                $parking->coordinates->longitude,
                $parking->coordinates->latitude,
                $parking->totalSpots,
            ),
            $allParkings,
        );

        $presenter->present($parkingSummaries);
    }
}
