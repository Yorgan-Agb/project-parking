<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Adapter\Controller\EnterParkingController;
use App\Adapter\Controller\ExitParkingController;
use App\Domain\Entity\Parking;
use App\Domain\Entity\Reservation;
use App\Domain\ValueObject\GpsCoordinates;
use App\Domain\ValueObject\OpeningHours;
use App\Domain\ValueObject\PricingGrid;
use App\Domain\ValueObject\PricingTier;
use App\Infrastructure\Clock\SystemClock;
use App\Infrastructure\IdGenerator\UuidGenerator;
use App\Infrastructure\Repository\InMemory\InMemoryParkingRepository;
use App\Infrastructure\Repository\InMemory\InMemoryReservationRepository;
use App\Infrastructure\Repository\InMemory\InMemoryStationnementRepository;
use App\UseCase\Stationnement\EnterParkingUseCase;
use App\UseCase\Stationnement\ExitParkingUseCase;

$pricingGrid = new PricingGrid([new PricingTier(0, 0.5), new PricingTier(60, 0.8)]);
$parking1 = new Parking('parking-1', 'owner-1', 'Parking Gare Centrale', new GpsCoordinates(47.9029, 1.9093), 50, $pricingGrid, new OpeningHours([]));

$reservationStart = new \DateTimeImmutable('-1 hour');
$reservationEnd = new \DateTimeImmutable('+2 hours');
$reservation1 = new Reservation('reservation-1', 'user-1', 'parking-1', $reservationStart, $reservationEnd, $parking1->priceFor($reservationStart, $reservationEnd));

$parkingRepository = new InMemoryParkingRepository([$parking1]);
$reservationRepository = new InMemoryReservationRepository([$reservation1]);
$stationnementRepository = new InMemoryStationnementRepository([]);
$clock = new SystemClock();
$idGenerator = new UuidGenerator();

$enterParkingController = new EnterParkingController(
    new EnterParkingUseCase($reservationRepository, $stationnementRepository, $idGenerator, $clock)
);
$exitParkingController = new ExitParkingController(
    new ExitParkingUseCase($stationnementRepository, $clock)
);

$steps = [
    ['Entrée user-1', fn() => $enterParkingController->handle('user-1', 'parking-1')],
    ['Deuxième entrée user-1', fn() => $enterParkingController->handle('user-1', 'parking-1')],
    ['Sortie user-1', fn() => $exitParkingController->handle('user-1', 'parking-1')],
    ['Entrée user-2 sans réservation', fn() => $enterParkingController->handle('user-2', 'parking-1')],
];

foreach ($steps as [$label, $action]) {
    $response = $action();
    echo $label . ' : ' . json_encode($response, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
