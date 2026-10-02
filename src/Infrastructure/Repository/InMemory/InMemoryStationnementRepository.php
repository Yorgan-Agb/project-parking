<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\InMemory;

use App\Domain\Entity\Stationnement;
use App\UseCase\Port\IStationnementRepository;

final class InMemoryStationnementRepository implements IStationnementRepository
{
    /** @var Stationnement[] */
    private array $stationnements;

    /**
     * @param Stationnement[] $stationnements
     */
    public function __construct(array $stationnements = [])
    {
        $this->stationnements = array_values($stationnements);
    }

    /**
     * @return Stationnement[]
     */
    public function findByUserId(string $userId): array
    {
        return array_values(array_filter(
            $this->stationnements,
            static fn(Stationnement $stationnement) => $stationnement->userId === $userId,
        ));
    }

    public function save(Stationnement $stationnement): void
    {
        foreach ($this->stationnements as $index => $existingStationnement) {
            if ($existingStationnement->id === $stationnement->id) {
                $this->stationnements[$index] = $stationnement;
                return;
            }
        }

        $this->stationnements[] = $stationnement;
    }
}
