<?php

declare(strict_types=1);

namespace App\Adapter\Controller;

use App\UseCase\Stationnement\ExitParkingRequest;
use App\UseCase\Stationnement\ExitParkingResponse;
use App\UseCase\Stationnement\ExitParkingUseCase;

final class ExitParkingController
{
    private readonly ExitParkingUseCase $exitParkingUseCase;

    public function __construct(ExitParkingUseCase $exitParkingUseCase)
    {
        $this->exitParkingUseCase = $exitParkingUseCase;
    }

    public function handle(string $userId, string $parkingId): ExitParkingResponse
    {
        return $this->exitParkingUseCase->execute(new ExitParkingRequest($userId, $parkingId));
    }
}
