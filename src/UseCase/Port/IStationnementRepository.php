<?php

declare(strict_types=1);

namespace App\UseCase\Port;

use App\Domain\Entity\Stationnement;

interface IStationnementRepository
{
    /** @return Stationnement[] */
    public function findByUserId(string $userId): array;

    public function save(Stationnement $stationnement): void;
}
