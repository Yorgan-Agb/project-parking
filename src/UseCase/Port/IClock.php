<?php

declare(strict_types=1);

namespace App\UseCase\Port;

interface IClock
{
    public function now(): \DateTimeImmutable;
}
