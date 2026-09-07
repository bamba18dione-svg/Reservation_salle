# Gestion des reservations de salles universitaires

Application web PHP orientee objet permettant de consulter les salles universitaires et de gerer leurs reservations.

## Objectifs

Le projet applique une architecture en couches afin de separer :

- le traitement HTTP ;
- la validation des donnees ;
- les objets de transport ;
- l'acces aux donnees ;
- les regles metier ;
- les vues.

Les reservations doivent respecter les regles de disponibilite, notamment l'absence de chevauchement avec une reservation confirmee.

## Technologies prevues

- PHP 8.2 ou 8.3 ;
- MySQL ;
- Composer ;
- FastRoute pour le routage ;
- Respect/Validation pour la validation ;
- Illuminate Database et Eloquent pour l'ORM ;
- PHP-DI pour l'injection de dependances ;
- PHP dotenv pour la configuration de l'environnement ;
- PHPUnit pour les tests.

## Prerequis

- PHP 8.2 ou une version plus recente compatible ;
- Composer ;
- MySQL ;
- Git.

## Installation

```bash
cp .env.example .env
composer install
```

Modifiez ensuite `.env` avec les acces de votre base MySQL. Le fichier `.env` reste local et n'est jamais versionne.

La configuration chargee depuis `.env` est centralisee dans `config/database.php`. Cette couche initialise `Capsule\\Manager`, demarre Eloquent et expose une connexion utilisable par les composants techniques.

La base de donnees et les tables seront ajoutees dans les prochaines etapes.

Pour creer les tables apres avoir configure `.env`, executez :

```bash
php database/migrations/001_create_salles_and_reservations.php
```

## Etape 2 : configuration Eloquent

La configuration utilise `vlucas/phpdotenv` pour lire l'environnement et `illuminate/database` pour demarrer Eloquent hors de Laravel.

Cette approche evite que les modeles, repositories et services lisent directement `getenv()` ou connaissent les secrets de connexion. Une configuration centralisee facilite aussi le remplacement de MySQL dans les tests.

Une configuration ecrite directement dans chaque modele serait plus rapide au debut, mais dupliquerait les parametres, melangerait configuration et metier et rendrait les tests plus fragiles.

## Etape 3 : modeles et schema

La migration cree les tables `salles` et `reservations` avec une cle etrangere entre elles. Elle verifie l'existence des tables avant leur creation afin de pouvoir etre relancee sans doublons.

Les modeles `Salle` et `Reservation` utilisent `$fillable` pour limiter les attributs assignables, des casts pour obtenir des types PHP coherents et des relations `hasMany` / `belongsTo` pour representer le domaine.

Une requete SQL directement placee dans un controleur serait plus courte, mais elle melangerait HTTP et persistance. De meme, lire `$_POST` dans un modele rendrait le modele difficile a tester et violerait la separation des responsabilites.

## Organisation cible

Le code sera organise selon les responsabilites suivantes :

- `public/` : point d'entree unique de l'application ;
- `config/` : configuration technique et conteneur ;
- `routes/` : declaration des routes ;
- `src/` : code applicatif ;
- `templates/` : vues PHP ;
- `database/` : migrations et donnees initiales ;
- `tests/` : tests unitaires et d'integration.

## Progression

| Version | Etape | Etat |
| --- | --- | --- |
| v0.0.0 | Initialisation du depot | En cours |
| v0.1.0 | Initialisation Composer | Termine |
| v0.2.0 | Configuration Eloquent | Termine |
| v0.3.0 | Modeles | Termine |
| v0.4.0 | Donnees initiales | A venir |
| v0.5.0 | Validation | A venir |
| v0.6.0 | DTO | A venir |
| v0.7.0 | Repositories | A venir |
| v0.8.0 | Services metier | A venir |
| v0.9.0 | Controleurs et vues | A venir |
| v0.10.0 | Routage | A venir |
| v0.11.0 | Conteneur PHP-DI | A venir |
| v0.12.0 | Tests | A venir |
| v1.0.0 | Version finale | A venir |

## Documentation

Les choix architecturaux, les avantages et les limites des approches retenues seront documentes dans `ARCHITECTURE.md` lors de la finalisation du projet.
