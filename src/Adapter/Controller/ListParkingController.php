<?php

declare(strict_types=1);

namespace App\Adapter\Controller;

use App\Adapter\Presenter\ListParkingHtmlPresenter;
use App\Adapter\View\ParkingMapView;
use App\UseCase\Parking\ListParkingUseCase;

final class ListParkingController
{
    private readonly ListParkingUseCase $listParkingUseCase;
    private readonly ParkingMapView $parkingMapView;

    public function __construct(ListParkingUseCase $listParkingUseCase, ParkingMapView $parkingMapView)
    {
        $this->listParkingUseCase = $listParkingUseCase;
        $this->parkingMapView = $parkingMapView;
    }

    public function handle(): string
    {
        $listParkingHtmlPresenter = new ListParkingHtmlPresenter();

        $this->listParkingUseCase->execute($listParkingHtmlPresenter);

        return $this->parkingMapView->render($listParkingHtmlPresenter->parkingSummaries);
    }
}
