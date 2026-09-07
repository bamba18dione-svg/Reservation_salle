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

## Etape 1 : initialisation Composer

Composer installe les dependances du projet et genere l'autoloading PSR-4. Les classes du namespace `App\` sont ainsi chargees automatiquement depuis le dossier `src/`.

Cette approche evite les `require` manuels et permet de declarer clairement les versions compatibles dans `composer.json`. Une installation manuelle des bibliotheques serait plus difficile a reproduire et ne garantirait pas les memes versions pour tous les environnements.

### Reponses aux questions de l'etape 1

#### 1. Quel est le role de Composer ?

Composer est le gestionnaire de dependances standard en PHP. Il installe et met a jour les bibliotheques tierces, resout leurs dependances et genere l'autoloader PSR-4 pour charger automatiquement les classes.

#### 2. Quelle difference existe entre `require` et `require-dev` ?

`require` contient les dependances necessaires au fonctionnement de l'application en production, comme l'ORM et le routeur. `require-dev` contient les outils utilises uniquement pendant le developpement et les tests, comme PHPUnit.

#### 3. Pourquoi faut-il versionner `composer.lock` ?

`composer.lock` fixe les versions exactes de tous les paquets installes. Le versionner garantit que les developpeurs et la production utilisent le meme ensemble de dependances.

#### 4. Pourquoi ne versionne-t-on pas `vendor/` ?

`vendor/` contient des fichiers generes automatiquement et volumineux. Il peut etre recree avec `composer install` grace a `composer.json` et `composer.lock`, donc il ne doit pas etre versionne.

## Etape 2 : configuration Eloquent

La configuration utilise `vlucas/phpdotenv` pour lire l'environnement et `illuminate/database` pour demarrer Eloquent hors de Laravel.

Cette approche evite que les modeles, repositories et services lisent directement `getenv()` ou connaissent les secrets de connexion. Une configuration centralisee facilite aussi le remplacement de MySQL dans les tests.

Une configuration ecrite directement dans chaque modele serait plus rapide au debut, mais dupliquerait les parametres, melangerait configuration et metier et rendrait les tests plus fragiles.

### Reponses aux questions de l'etape 2

#### 1. Quel role joue `Capsule\Manager` ?

`Illuminate\Database\Capsule\Manager` configure le gestionnaire de connexions et permet de demarrer Eloquent en dehors de Laravel. Avec `setAsGlobal()` et `bootEloquent()`, il rend les modeles Eloquent utilisables par l'application.

#### 2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Eloquent est distribue comme un composant autonome via `illuminate/database`. Il a besoin d'une connexion configuree et de son infrastructure ORM, mais pas du noyau HTTP, des controleurs ou des vues de Laravel.

#### 3. Ou doit se trouver le demarrage de l'ORM ?

Le demarrage doit se trouver dans la configuration technique, ici `config/database.php`, et etre execute une seule fois pendant l'amorcage de l'application. Cela evite de dupliquer la connexion dans les modeles, repositories ou services.

#### 4. Quelle difference existe entre ORM et SQL ecrit a la main ?

Avec SQL ecrit a la main, le developpeur construit directement les requetes envoyees a la base. Avec un ORM, il manipule des objets et des relations PHP qu'Eloquent traduit en SQL. L'ORM reduit le code repetitif, mais SQL reste parfois necessaire pour des requetes tres specifiques ou optimisees.

## Etape 3 : modeles et schema

La migration cree les tables `salles` et `reservations` avec une cle etrangere entre elles. Elle verifie l'existence des tables avant leur creation afin de pouvoir etre relancee sans doublons.

Les modeles `Salle` et `Reservation` utilisent `$fillable` pour limiter les attributs assignables, des casts pour obtenir des types PHP coherents et des relations `hasMany` / `belongsTo` pour representer le domaine.

Une requete SQL directement placee dans un controleur serait plus courte, mais elle melangerait HTTP et persistance. De meme, lire `$_POST` dans un modele rendrait le modele difficile a tester et violerait la separation des responsabilites.

### Reponses aux questions de l'etape 3

#### 1. Quel type de relation Eloquent avons-nous utilise ?

Nous avons utilise une relation `HasMany` entre `Salle` et `Reservation`, car une salle peut posseder plusieurs reservations. Le modele `Reservation` utilise la relation inverse `BelongsTo`, car chaque reservation appartient a une seule salle.

#### 2. Pourquoi declarer `$fillable` ou `$guarded` ?

`$fillable` protege les modeles contre l'assignation de masse non controlee. Nous declarons explicitement les attributs autorises a etre remplis, afin qu'un champ inattendu provenant d'une requete HTTP ne puisse pas modifier une colonne sensible. `$guarded` est une alternative qui declare les attributs interdits, mais `$fillable` est plus explicite et plus sur dans ce projet.

#### 3. Pourquoi convertir `active` en booleen ?

La base de donnees peut retourner un booleen sous forme d'entier `0` ou `1`. Le cast Eloquent garantit que `active` est manipule comme un vrai `bool` en PHP, ce qui rend les conditions metier plus claires et evite les comparaisons incoherentes.

#### 4. Pourquoi convertir les dates en objets ?

Les casts `immutable_datetime` transforment les dates SQL en objets `DateTimeImmutable`. Cela permet de comparer, additionner et soustraire des durees de maniere fiable, tout en evitant la modification accidentelle d'une date existante. Les regles de chevauchement et de duree pourront ainsi utiliser des operations datees robustes dans les services metier.

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
