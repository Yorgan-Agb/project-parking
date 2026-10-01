<?php

declare(strict_types=1);

namespace App\UseCase\Reservation;

final class CreateReservationResponse
{
    public readonly bool $success;
    public readonly ?string $reservationId;
    public readonly ?string $parkingId;
    public readonly ?string $start;
    public readonly ?string $end;
    public readonly ?float $price;
    public readonly ?string $errorCode;
    public readonly ?string $errorMessage;

    private function __construct(
        bool $success,
        ?string $reservationId,
        ?string $parkingId,
        ?string $start,
        ?string $end,
        ?float $price,
        ?string $errorCode,
        ?string $errorMessage
    ) {
        $this->success = $success;
        $this->reservationId = $reservationId;
        $this->parkingId = $parkingId;
        $this->start = $start;
        $this->end = $end;
        $this->price = $price;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    public static function success(string $reservationId, string $parkingId, string $start, string $end, float $price): self
    {
        return new self(true, $reservationId, $parkingId, $start, $end, $price, null, null);
    }

    public static function failure(string $errorCode, string $errorMessage): self
    {
        return new self(false, null, null, null, null, null, $errorCode, $errorMessage);
    }
}
