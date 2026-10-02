<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Adapter\Controller\CreateReservationController;
use App\Adapter\Controller\EnterParkingController;
use App\Adapter\Controller\ExitParkingController;
use App\Adapter\Controller\ListParkingController;
use App\Adapter\View\ParkingMapView;
use App\Infrastructure\Clock\SystemClock;
use App\Infrastructure\IdGenerator\UuidGenerator;
use App\Infrastructure\Repository\Json\JsonParkingRepository;
use App\Infrastructure\Repository\Json\JsonReservationRepository;
use App\Infrastructure\Repository\Json\JsonStationnementRepository;
use App\UseCase\Parking\ListParkingUseCase;
use App\UseCase\Reservation\CreateReservationUseCase;
use App\UseCase\Stationnement\EnterParkingUseCase;
use App\UseCase\Stationnement\ExitParkingUseCase;

$dataDir = __DIR__ . '/../data';

$parkingRepository = new JsonParkingRepository($dataDir . '/parkings.json');
$reservationRepository = new JsonReservationRepository($dataDir . '/reservations.json');
$stationnementRepository = new JsonStationnementRepository($dataDir . '/stationnements.json');

$clock = new SystemClock();
$idGenerator = new UuidGenerator();

$listParkingUseCase = new ListParkingUseCase($parkingRepository);
$enterParkingUseCase = new EnterParkingUseCase($reservationRepository, $stationnementRepository, $idGenerator, $clock);
$exitParkingUseCase = new ExitParkingUseCase($stationnementRepository, $clock);
$createReservationUseCase = new CreateReservationUseCase($parkingRepository, $reservationRepository, $idGenerator, $clock);

$listParkingController = new ListParkingController($listParkingUseCase, new ParkingMapView());
$enterParkingController = new EnterParkingController($enterParkingUseCase);
$exitParkingController = new ExitParkingController($exitParkingUseCase);
$createReservationController = new CreateReservationController($createReservationUseCase);

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

if ($path === '/reservations') {
    $userId = (string) ($_GET['userId'] ?? '');
    $parkingId = (string) ($_GET['parkingId'] ?? '');
    $start = (string) ($_GET['start'] ?? '');
    $end = (string) ($_GET['end'] ?? '');

    $response = $createReservationController->handle($userId, $parkingId, $start, $end);

    http_response_code($response->success ? 200 : 400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    return;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Page introuvable';