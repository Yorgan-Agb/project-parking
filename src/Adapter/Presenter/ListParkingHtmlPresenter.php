<?php

declare(strict_types=1);

namespace App\Adapter\Presenter;

use App\UseCase\Parking\ParkingSummaryResponse;
use App\UseCase\Port\IListParkingPresenter;

final class ListParkingHtmlPresenter implements IListParkingPresenter
{
    /** @var ParkingSummaryResponse[] */
    public array $parkingSummaries = [];

    /**
     * @param ParkingSummaryResponse[] $parkings
     */
    public function present(array $parkings): void
    {
        $this->parkingSummaries = $parkings;
    }
}
