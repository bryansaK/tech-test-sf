# NOTES – Résumé rapide

## Ce que j’ai fait

- **Liste d’événements** : endpoint `GET /events` paginé (`page`, `limit`) via `PaginationService`, avec filtres sur la date (from/to) et la localisation. Les données retournent des `EventDTO` sérialisés avec le groupe `public`.

- **Détail d’un événement** : endpoint `GET /events/{id}` qui récupère un événement par UUID via `EventService`, renvoie un `EventDTO` ou une 404 propre si l’ID est invalide ou introuvable.

- **Création / suppression d’événements** : endpoints `POST /events` et `DELETE /events/{id}` pour gérer le cycle de vie des événements côté API (validation simple, persistance Doctrine, suppression sécurisée).

- **Auth utilisateur (login / register)** : endpoints `/login` et `/register` via `UserController` et `UserService`, avec création d’utilisateur, hash du mot de passe (bcrypt) et vérification basique des credentials.

- **Calendrier utilisateur** : mise en place de l’entité `Calendar`, du `CalendarService` et du `CalendarController` pour gérer les liens User/Event via :
	- `GET /users/{userId}/calendar` – récupération des événements du calendrier d’un user (paginée, en `EventDTO`),
	- `POST /users/{userId}/calendar` – ajout d’un événement au calendrier,
	- `DELETE /users/{userId}/calendar/{eventId}` – suppression du lien calendrier.

- **Documentation & debug** : intégration de Swagger / NelmioApiDoc pour documenter et tester facilement les routes de l’API.

- **Tests** : ajout de tests fonctionnels (PHPUnit) pour les principaux endpoints et d’une commande dédiée dans le `Makefile` pour les exécuter rapidement.

## Choix techniques principaux

- **DTO & sérialisation** : utilisation de `EventDTO` (et DTO associés) pour découpler les entités Doctrine de la surface de l’API, avec groupes de sérialisation (`public`) pour contrôler précisément les champs exposés.

- **Services métier** : extraction de la logique des contrôleurs vers des services dédiés (`EventService`, `UserService`, `CalendarService`, `PaginationService`) pour garder des contrôleurs fins et faciliter les tests.

- **Pagination et filtres** : service de pagination réutilisable qui calcule `page`, `limit`, `offset`, et filtres appliqués côté repository pour limiter la charge sur l’API (pas de gros dumps de données).

- **Robustesse / DX** :
	- gestion propre des erreurs (404, 400) et messages JSON clairs,
	- usage de types stricts (`declare(strict_types=1)`) et des attributs Symfony/Doctrine modernes,
	- utilisation de NelmioApiDoc pour exposer les contrats des endpoints.

- **Tests & outillage** : intégration des tests PHPUnit dans le workflow via le `Makefile`, pour pouvoir rapidement vérifier que les endpoints critiques restent fonctionnels après modification.
