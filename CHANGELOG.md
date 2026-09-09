# Changelog

Toutes les modifications importantes du projet sont documentees dans ce fichier.

## [v0.1.0] - 2026-09-06

### Ajoute

- Initialisation de Composer et de l'autoloading PSR-4.
- Ajout des dependances applicatives et de PHPUnit.
- Creation de l'arborescence initiale du projet.

## [v0.2.0] - 2026-09-06

### Ajoute

- Chargement de la configuration avec `phpdotenv`.
- Configuration centralisee de `Capsule\\Manager`.
- Demarrage d'Eloquent hors de Laravel.
- Documentation de la configuration de la base de donnees.

### Securite

- Suppression du mot de passe de `.env.example`.

## [v0.3.0] - 2026-09-07

### Ajoute

- Migration idempotente des tables `salles` et `reservations`.
- Modele `Salle` avec sa relation vers les reservations.
- Modele `Reservation` avec sa relation vers la salle.
- Casts Eloquent pour les nombres, booleens et dates immuables.

## [v0.4.0] - 2026-09-07

### Ajoute

- Creation du seeder `database/seed.php`.
- Ajout des cinq salles initiales demandees par le sujet.
- Protection contre les doublons avec `firstOrCreate` sur le nom et le batiment.

## [v0.5.0] - 2026-09-07

### Ajoute

- Contrat commun `ValidatorInterface`.
- Objet `ValidationResult` avec erreurs par champ et donnees acceptees.
- Validation des salles et des reservations avec Respect/Validation.
- Tests unitaires des donnees invalides et valides.

## [v0.6.0] - 2026-09-07

### Ajoute

- DTO immuables `CreerSalleDTO` et `CreerReservationDTO`.
- Builders dedies pour construire les DTO avec des donnees typees.
- Conversion des dates en `DateTimeImmutable` au niveau du Builder.
- Tests unitaires de construction des DTO et de rejet des DTO incomplets.

## [v0.7.0] - 2026-09-07

### Ajoute

- Contrats `SalleRepositoryInterface` et `ReservationRepositoryInterface`.
- Implementations Eloquent pour les salles et les reservations.
- Filtrage des reservations par salle.
- Recherche des conflits entre reservations confirmees.
- Annulation d'une reservation via le repository.

## [v0.8.0] - 2026-09-07

### Ajoute

- Service de creation des reservations.
- Service d'annulation des reservations.
- Verification de la disponibilite et des regles de duree et de futur.
- Exceptions metier pour les salles indisponibles et reservations introuvables.
- Tests unitaires avec repositories en memoire.

## [v0.9.0] - 2026-09-08

### Ajoute

- Controleurs `SalleController` et `ReservationController`.
- Rendu centralise avec `ViewRenderer`.
- Templates pour les salles, reservations et erreurs 404.
- Validation des formulaires avant construction des DTO.
- Redirection apres les operations POST reussies.
- Echappement des sorties dynamiques dans les vues.

## [v0.10.0] - 2026-09-08

### Ajoute

- Declaration des routes dans `routes/web.php`.
- Dispatcher FastRoute dans `App\\Http\\Router`.
- Gestion des reponses 404 et 405.
- En-tete `Allow` pour les methodes non autorisees.
- Transmission des parametres dynamiques aux controleurs.
- Tests unitaires du routage.

## [v0.11.0] - 2026-09-08

### Ajoute

- Configuration PHP-DI avec autowiring et definitions d'interfaces.
- Factories pour Eloquent, le routeur et le rendu des vues.
- Classe `Application` comme Front Controller applicatif.
- Initialisation du conteneur dans `public/index.php`.
- Resolution des dependances par injection de constructeur.

## [v0.0.0] - 2026-09-06

### Ajoute

- Initialisation du depot Git.
- Creation de la branche `main`.
- Creation de la documentation initiale du projet.
- Ajout des regles d'exclusion Git.
- Creation de la structure du journal des versions.
## [v0.12.0] - 2026-10-20
### Ajoute
- Tests d'integration Eloquent (SQLite en memoire) : CRUD, relations, chevauchements, annulation.
- Tableau de bord (`DashboardController`) avec statistiques.
- Conteneurisation Docker (`Dockerfile`, `docker-compose.yml` avec MySQL 8.0).
- Page d'erreur 405.

### Corrige
- Mise a jour des vues et du style CSS.

## [1.0.0] - 2026-10-20
Version finale — branche `release/1.0.0`.

### Ajoute
- Systeme de messages flash (succes / erreur) via `App\Support\Flash` affiches dans le layout.
- Styles CSS des messages flash (`.flash--success`, `.flash--error`).
- Gestion globale des exceptions dans `Application::runSafe()` : page 500 dediee, message generique a l'utilisateur, detail en log.
- Page d'erreur 500 (`templates/error/500.php`).
- Diagramme de classes (`docs/diagramme-classes.md` + PlantUML).
- Verification de l'installation depuis un depot fraichement clone.

### Corrige
- Tests unitaires `ValidationTest` alignes sur l'API actuelle des validateurs (`ValidationResult::errors()`, champ `email`, champ `batiment`).
- Flash de confirmation ajoute apres creation de salle, mise a jour de salle, creation et annulation de reservation.
