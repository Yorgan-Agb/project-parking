<?php

declare(strict_types=1);

namespace App\UseCase\Stationnement;

final class ExitParkingResponse
{
    public readonly bool $success;
    public readonly string $message;

    private function __construct(bool $success, string $message)
    {
        $this->success = $success;
        $this->message = $message;
    }

    public static function success(string $message): self
    {
        return new self(true, $message);
    }

    public static function failure(string $message): self
    {
        return new self(false, $message);
    }
}
