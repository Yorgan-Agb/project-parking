<?php

declare(strict_types=1);

namespace App\UseCase\Reservation;

use App\Domain\Entity\Reservation;

final class CreateReservationResponse
{
    public readonly bool $success;
    public readonly ?Reservation $reservation;
    public readonly ?string $errorCode;
    public readonly ?string $errorMessage;

    private function __construct(
        bool $success,
        ?Reservation $reservation,
        ?string $errorCode,
        ?string $errorMessage
    ) {
        $this->success = $success;
        $this->reservation = $reservation;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    public static function success(Reservation $reservation): self
    {
        return new self(true, $reservation, null, null);
    }

    public static function failure(string $errorCode, string $errorMessage): self
    {
        return new self(false, null, $errorCode, $errorMessage);
    }
}
