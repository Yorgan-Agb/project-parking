<?php

declare(strict_types=1);

namespace App\UseCase\Stationnement;

use App\UseCase\Port\IClock;
use App\UseCase\Port\IStationnementRepository;

final class ExitParkingUseCase
{
    private readonly IStationnementRepository $stationnementRepository;
    private readonly IClock $clock;

    public function __construct(IStationnementRepository $stationnementRepository, IClock $clock)
    {
        $this->stationnementRepository = $stationnementRepository;
        $this->clock = $clock;
    }

    public function execute(ExitParkingRequest $request): ExitParkingResponse
    {
        $now = $this->clock->now();
        $userStationnements = $this->stationnementRepository->findByUserId($request->userId);

        $activeStationnement = null;
        foreach ($userStationnements as $stationnement) {
            if ($stationnement->parkingId === $request->parkingId && $stationnement->isOngoing()) {
                $activeStationnement = $stationnement;
                break;
            }
        }

        if ($activeStationnement === null) {
            return ExitParkingResponse::failure('Aucun stationnement en cours dans ce parking');
        }

        $exitedStationnement = $activeStationnement->withExit($now);

        $this->stationnementRepository->save($exitedStationnement);

        return ExitParkingResponse::success('Sortie enregistrée');
    }
}
