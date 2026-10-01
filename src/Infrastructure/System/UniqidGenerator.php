<?php

declare(strict_types=1);

namespace App\Infrastructure\System;

use App\UseCase\Port\IIdGenerator;

final class UniqidGenerator implements IIdGenerator
{
    public function generate(): string
    {
        return uniqid('', true);
    }
}
