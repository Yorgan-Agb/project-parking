<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Adapter\Controller\CreateReservationController;
use App\Infrastructure\Repository\Json\JsonParkingRepository;
use App\Infrastructure\Repository\Json\JsonReservationRepository;
use App\Infrastructure\System\SystemClock;
use App\Infrastructure\System\UniqidGenerator;
use App\UseCase\Reservation\CreateReservationUseCase;

$dataDir = __DIR__ . '/../data';

$parkingRepository = new JsonParkingRepository($dataDir . '/parkings.json');
$reservationRepository = new JsonReservationRepository($dataDir . '/reservations.json');

$clock = new SystemClock();
$idGenerator = new UniqidGenerator();

$createReservationUseCase = new CreateReservationUseCase(
    $parkingRepository,
    $reservationRepository,
    $idGenerator,
    $clock,
);

$createReservationController = new CreateReservationController($createReservationUseCase);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

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
