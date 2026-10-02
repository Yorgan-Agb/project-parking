<?php

declare(strict_types=1);

namespace App\UseCase\Port;

use App\UseCase\Parking\ParkingSummaryResponse;

interface IListParkingPresenter
{
    /** @param ParkingSummaryResponse[] $parkings */
    public function present(array $parkings): void;
}
