<?php

declare(strict_types=1);

namespace App\Adapter\View;

use App\UseCase\Parking\ParkingSummaryResponse;

final class ParkingMapView
{
    /**
     * @param ParkingSummaryResponse[] $parkings
     */
    public function render(array $parkings): string
    {
        $parkingsJson = json_encode($parkings, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Parkings</title>
    <style>
        #map { height: 100vh; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
</head>
<body>
    <div id="map"></div>
    <script>
        const parkings = {$parkingsJson};

        const map = L.map('map').setView([47.9029, 1.9093], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        parkings.forEach((parking) => {
            const popupText = escapeHtml(parking.name) + " - " + parking.totalSpots + " places";

            L.marker([parking.latitude, parking.longitude])
                .addTo(map)
                .bindPopup(popupText);
        });
    </script>
</body>
</html>
HTML;
    }
}
