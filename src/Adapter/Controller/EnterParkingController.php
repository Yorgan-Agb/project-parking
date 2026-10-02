<?php

declare(strict_types=1);

namespace App\Adapter\Controller;

use App\UseCase\Stationnement\EnterParkingRequest;
use App\UseCase\Stationnement\EnterParkingResponse;
use App\UseCase\Stationnement\EnterParkingUseCase;

final class EnterParkingController
{
    private readonly EnterParkingUseCase $enterParkingUseCase;

    public function __construct(EnterParkingUseCase $enterParkingUseCase)
    {
        $this->enterParkingUseCase = $enterParkingUseCase;
    }

    public function handle(string $userId, string $parkingId): EnterParkingResponse
    {
        return $this->enterParkingUseCase->execute(new EnterParkingRequest($userId, $parkingId));
    }
}
