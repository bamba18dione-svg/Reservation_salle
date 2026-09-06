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

Les commandes d'installation et de configuration seront ajoutees au fur et a mesure des etapes du projet.

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
| v0.1.0 | Initialisation Composer | A venir |
| v0.2.0 | Configuration Eloquent | A venir |
| v0.3.0 | Modeles | A venir |
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
