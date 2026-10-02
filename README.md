# Système de Parking Partagé

Projet Master 1 ADL CODA — POC d'un système de parking partagé.

## Architecture

```
src/
├── Domain/           # Entités et Value Objects — aucune dépendance externe
│   ├── Entity/        (Parking, Reservation, Stationnement, User, ParkingOwner)
│   └── ValueObject/    (GpsCoordinates, PricingGrid, PricingTier, OpeningHours, TimeSlot)
├── UseCase/           # Logique métier, indépendante de toute technologie
│   ├── Port/           Interfaces (IParkingRepository, IReservationRepository, IClock, IIdGenerator...)
│   ├── Reservation/     CreateReservationUseCase + Request/Response
│   ├── Stationnement/   EnterParkingUseCase, ExitParkingUseCase + Request/Response
│   └── Parking/         ListParkingUseCase
├── Adapter/            # Point d'entrée externe (HTTP) et présentation
│   ├── Controller/      Transforme une requête HTTP en appel de Use Case
│   ├── Presenter/
│   └── View/            ParkingMapView (carte Leaflet)
└── Infrastructure/     # Implémentations concrètes des Ports
    ├── Repository/Json/ Persistance JSON (interchangeable avec une autre implémentation
    │                     sans toucher au Domain ni aux Use Cases)
    ├── Clock/
    └── IdGenerator/
```

Le stockage se fait dans des fichiers JSON (`data/*.json`). Changer de système de stockage (ex: MySQL) ne nécessite qu'une nouvelle implémentation des interfaces `IParkingRepository`/`IReservationRepository`/`IStationnementRepository` dans `Infrastructure/`, sans modifier le Domain ni les Use Cases.

## Use cases implémentés

| Use case | Route | Méthode | Description |
|---|---|---|---|
| **Afficher la liste des parkings géolocalisés** | `/` | GET | Affiche une carte Leaflet avec les parkings disponibles |
| **Créer une réservation** | `/reservations` | GET | Réserve une place pour un créneau donné, avec vérification des horaires d'ouverture et de la capacité disponible |
| **Entrer dans un parking** | `/enter` | GET | Autorise l'entrée si une réservation active correspond au créneau en cours |
| **Sortir d'un parking** | `/exit` | GET | Enregistre l'heure de sortie et libère la place |

## Installation

```bash
composer install
```

## Lancer le serveur

```bash
php -S localhost:8000 -t public
```

## Tester les use cases

### Afficher la carte
Ouvrir dans un navigateur :
```
http://localhost:8000/
```

### Créer une réservation
```
http://localhost:8000/reservations?userId=user-1&parkingId=parking-1&start=2026-10-10T10:00:00%2B02:00&end=2026-10-10T12:00:00%2B02:00
```
Réponses possibles (`errorCode`) : `INVALID_RANGE`, `PAST_RESERVATION`, `PARKING_NOT_FOUND`, `PARKING_CLOSED`, `NO_AVAILABILITY`.

### Entrer dans un parking
Nécessite une réservation active (créée ci-dessus) pour le créneau en cours :
```
http://localhost:8000/enter?userId=user-1&parkingId=parking-1
```

### Sortir d'un parking
```
http://localhost:8000/exit?userId=user-1&parkingId=parking-1
```

## Stockage des données

Les données sont stockées dans `data/` :
- `parkings.json` — parkings disponibles (coordonnées GPS, grille tarifaire, horaires d'ouverture)
- `reservations.json` — réservations effectuées
- `stationnements.json` — entrées/sorties enregistrées
- `users.json` / `owners.json` — utilisateurs et propriétaires de parkings
