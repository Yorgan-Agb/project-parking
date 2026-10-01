<?php

declare(strict_types=1);

namespace App\Infrastructure\Clock;

use App\UseCase\Port\IClock;

final class SystemClock implements IClock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
