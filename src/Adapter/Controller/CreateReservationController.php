<?php

declare(strict_types=1);

namespace App\Adapter\Controller;

use App\UseCase\Reservation\CreateReservationRequest;
use App\UseCase\Reservation\CreateReservationResponse;
use App\UseCase\Reservation\CreateReservationUseCase;

final class CreateReservationController
{
    private readonly CreateReservationUseCase $createReservationUseCase;

    public function __construct(CreateReservationUseCase $createReservationUseCase)
    {
        $this->createReservationUseCase = $createReservationUseCase;
    }

    public function handle(string $userId, string $parkingId, string $start, string $end): CreateReservationResponse
    {
        try {
            $startDate = new \DateTimeImmutable($start);
            $endDate = new \DateTimeImmutable($end);
        } catch (\Exception) {
            return CreateReservationResponse::failure(
                'INVALID_DATE_FORMAT',
                'Le format des dates de début ou de fin est invalide.',
            );
        }

        $request = new CreateReservationRequest($userId, $parkingId, $startDate, $endDate);

        return $this->createReservationUseCase->execute($request);
    }
}