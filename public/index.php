<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Adapter\Controller\EnterParkingController;
use App\Adapter\Controller\ExitParkingController;
use App\Adapter\Controller\ListParkingController;
use App\Adapter\View\ParkingMapView;
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
use App\Infrastructure\Repository\Json\JsonParkingRepository;
use App\Infrastructure\Repository\Json\JsonReservationRepository;
use App\Infrastructure\Repository\Json\JsonStationnementRepository;
use App\UseCase\Parking\ListParkingUseCase;
use App\UseCase\Stationnement\EnterParkingUseCase;
use App\UseCase\Stationnement\ExitParkingUseCase;

$storage = 'json';

if ($storage === 'memory') {
    $pricingGrid = new PricingGrid([new PricingTier(0, 0.5), new PricingTier(60, 0.8)]);
    $openingHours = new OpeningHours([]);

    $parking1 = new Parking('parking-1', 'owner-1', 'Parking Gare Centrale', new GpsCoordinates(47.9029, 1.9093), 50, $pricingGrid, $openingHours);
    $parking2 = new Parking('parking-2', 'owner-1', 'Parking Gare des Aubrais', new GpsCoordinates(47.9266, 1.9076), 30, $pricingGrid, $openingHours);
    $parking3 = new Parking('parking-3', 'owner-1', 'Parking Olivet Centre', new GpsCoordinates(47.8633, 1.8997), 20, $pricingGrid, $openingHours);

    $reservationStart = new \DateTimeImmutable('-1 hour');
    $reservationEnd = new \DateTimeImmutable('+2 hours');
    $reservation1 = new Reservation('reservation-1', 'user-1', 'parking-1', $reservationStart, $reservationEnd, $parking1->priceFor($reservationStart, $reservationEnd));

    $parkingRepository = new InMemoryParkingRepository([$parking1, $parking2, $parking3]);
    $reservationRepository = new InMemoryReservationRepository([$reservation1]);
    $stationnementRepository = new InMemoryStationnementRepository([]);
} else {
    $dataDir = __DIR__ . '/../data';

    $parkingRepository = new JsonParkingRepository($dataDir . '/parkings.json');
    $reservationRepository = new JsonReservationRepository($dataDir . '/reservations.json');
    $stationnementRepository = new JsonStationnementRepository($dataDir . '/stationnements.json');
}

$clock = new SystemClock();
$idGenerator = new UuidGenerator();

$listParkingUseCase = new ListParkingUseCase($parkingRepository);
$enterParkingUseCase = new EnterParkingUseCase($reservationRepository, $stationnementRepository, $idGenerator, $clock);
$exitParkingUseCase = new ExitParkingUseCase($stationnementRepository, $clock);

$listParkingController = new ListParkingController($listParkingUseCase, new ParkingMapView());
$enterParkingController = new EnterParkingController($enterParkingUseCase);
$exitParkingController = new ExitParkingController($exitParkingUseCase);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    echo $listParkingController->handle();
    return;
}

if ($path === '/enter' || $path === '/exit') {
    $userId = (string) ($_GET['userId'] ?? '');
    $parkingId = (string) ($_GET['parkingId'] ?? '');

    $response = $path === '/enter'
        ? $enterParkingController->handle($userId, $parkingId)
        : $exitParkingController->handle($userId, $parkingId);

    http_response_code($response->success ? 200 : 403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    return;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Page introuvable';
