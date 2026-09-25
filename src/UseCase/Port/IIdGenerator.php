<?php

declare(strict_types=1);

namespace App\UseCase\Port;

interface IIdGenerator
{
    public function generate(): string;
}
