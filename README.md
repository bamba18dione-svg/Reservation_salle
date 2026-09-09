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

## Etape 4 : donnees initiales

Le script `database/seed.php` ajoute les cinq salles demandees par le sujet : Amphitheatre A, Salle B12, Laboratoire Chimie, Salle Informatique 1 et Salle de reunion.

Pour executer le seeder :

```bash
php database/seed.php
```

Le script utilise `firstOrCreate` avec le couple `nom` et `batiment` comme identifiant fonctionnel. Il peut donc etre execute plusieurs fois sans creer de doublons. Une insertion manuelle sans verification serait plus simple, mais elle ajouterait les memes salles a chaque execution.

### Reponses aux questions de l'etape 4

#### 1. Quelle difference existe entre migration et seeder ?

Une migration definit et fait evoluer la structure de la base de donnees : tables, colonnes, index et cles etrangeres. Un seeder ajoute des donnees initiales ou de demonstration dans une structure deja creee. La migration construit le schema, tandis que le seeder le peuple.

#### 2. Pourquoi les donnees initiales doivent-elles etre reproductibles ?

Un seeder reproductible peut etre execute apres une nouvelle installation, par un autre developpeur ou dans un environnement de test sans produire un resultat different ou incoherent. Cela facilite l'installation et permet de retrouver un etat de depart connu.

#### 3. Comment empecher les doublons ?

Le script recherche d'abord une salle avec `firstOrCreate` selon son nom et son batiment. Si elle existe, elle est reutilisee ; sinon, elle est creee. Pour renforcer cette garantie au niveau de la base, une contrainte d'unicite sur `nom` et `batiment` pourrait aussi etre ajoutee dans une migration.

## Etape 5 : validation

La validation est organisee autour de `ValidatorInterface`, `ValidationResult`, `SalleValidator` et `ReservationValidator`. Les validateurs utilisent Respect/Validation pour verifier la forme des donnees avant leur utilisation.

Les validateurs retournent toutes les erreurs par champ ainsi que les donnees acceptees. Ils ne sauvegardent rien et ne comparent pas les dates entre elles : ces responsabilites appartiennent respectivement aux repositories et aux services metier.

Les tests unitaires couvrent notamment l'email invalide, le responsable vide, la capacite negative, le type inconnu et la date incorrecte :

```bash
vendor/bin/phpunit
```

Mettre les regles directement dans les controleurs serait plus rapide pour un prototype, mais cela dupliquerait la logique et rendrait les tests difficiles. Une validation manuelle avec des `if` serait aussi plus longue a maintenir que des regles composables.

### Reponses aux questions de l'etape 5

#### 1. Pourquoi separer la validation syntaxique des regles metier ?

La validation syntaxique verifie la forme d'une donnee : type, longueur, email ou date lisible. Les regles metier verifient le sens dans le contexte de l'application, comme une salle active, une date future ou l'absence de chevauchement. Cette separation evite de transformer les validateurs en services metier difficiles a reutiliser.

#### 2. Pourquoi creer une interface de validation ?

`ValidatorInterface` impose un contrat commun aux validateurs. Les controleurs et le conteneur peuvent donc dependre de ce contrat plutot que d'une implementation concrete, ce qui respecte l'inversion de dependance et facilite le remplacement ou le test d'un validateur.

#### 3. Pourquoi le validateur ne doit-il pas enregistrer les donnees ?

Le validateur doit avoir une seule responsabilite : verifier les donnees. L'enregistrement appartient au service ou au repository. Melanger ces responsabilites rendrait les effets de bord imprevisibles et empêcherait de valider un formulaire sans modifier la base.

#### 4. Comment retourner plusieurs erreurs en une seule fois ?

`ValidationResult` conserve un tableau d'erreurs indexe par nom de champ. Le validateur teste chaque champ au lieu de s'arreter a la premiere erreur, ce qui permet de reafficher le formulaire avec toutes les corrections necessaires.

## Etape 6 : objets de transport et Builder

Les classes `CreerSalleDTO` et `CreerReservationDTO` transportent des donnees deja validees et correctement typees. Elles sont immuables et ne connaissent ni Eloquent, ni `$_POST`, ni les services metier.

La construction est faite au niveau du DTO avec `CreerSalleDTOBuilder` et `CreerReservationDTOBuilder`. Le Builder expose une API fluide, convertit les dates en `DateTimeImmutable` et retourne le DTO avec `build()`. Un DTO ne contient pas de `save()` : la persistance reste la responsabilite du repository.

Cette approche evite de transmettre un tableau HTTP brut a la couche metier. Construire directement un modele Eloquent depuis `$_POST` serait plus court, mais melangerait transport HTTP, typage et persistance. Un constructeur avec beaucoup d'arguments serait possible, mais le Builder rend explicite chaque champ et controle les DTO incomplets.

### Reponses aux questions de l'etape 6

#### 1. Quelle difference existe entre DTO et modele Eloquent ?

Un DTO transporte des donnees entre les couches sans acceder a la base. Un modele Eloquent represente une entite persistante, connait une table et peut charger ou enregistrer des donnees. Le DTO protege donc le domaine contre les details HTTP et ORM.

#### 2. Pourquoi le DTO ne doit-il pas appeler `save()` ?

Le DTO a une seule responsabilite : transporter des valeurs typees. Appeler `save()` lui donnerait une responsabilite de persistance, creerait un couplage avec Eloquent et empecherait de reutiliser le DTO dans un test ou une autre infrastructure.

#### 3. A quel moment transforme-t-on les chaines en dates ?

Les chaines issues du formulaire sont validees syntaxiquement par le validateur, puis converties en `DateTimeImmutable` par le `CreerReservationDTOBuilder`. Le service recoit ainsi des dates typees et ne depend plus du format HTTP.

#### 4. Le DTO doit-il contenir la regle de chevauchement ?

Non. Le DTO transporte les dates, mais ne decide pas si elles sont disponibles. La regle de chevauchement depend de la base, de la salle et des reservations existantes ; elle appartient donc au service metier.

## Etape 7 : repositories

Les contrats `SalleRepositoryInterface` et `ReservationRepositoryInterface` definissent les operations necessaires a l'application. Les classes `EloquentSalleRepository` et `EloquentReservationRepository` les implementent avec Eloquent.

Les controleurs et les services ne doivent donc pas appeler `Salle::query()`, `Reservation::where(...)` ou `save()` directement. La recherche de conflit est centralisee dans `EloquentReservationRepository` et ignore les reservations dont le statut est `annulee`.

Cette abstraction facilite les tests unitaires des services avec des doublures en memoire. Utiliser directement Eloquent dans les services serait plus court, mais couplerait la logique metier a l'ORM et rendrait plus difficile le remplacement de la persistance. Le Repository ajoute toutefois une couche supplementaire a maintenir ; ici, elle est justifiee par la contrainte architecturale et l'inversion de dependance.

### Reponses aux questions de l'etape 7

#### 1. Eloquent constitue-t-il deja un acces aux donnees ?

Oui. Eloquent fournit deja un ORM, un Query Builder et un Active Record capables de lire et d'enregistrer les donnees. Il constitue donc techniquement un acces aux donnees complet.

#### 2. Pourquoi ajouter un Repository au-dessus d'Eloquent ?

Le Repository isole l'ORM derriere un contrat metier. Les services dependent d'une interface stable et ne connaissent pas les details de construction des requetes Eloquent. Cela facilite les tests, le remplacement de l'ORM et la centralisation des requetes complexes comme la recherche de chevauchement.

#### 3. Cette abstraction est-elle toujours necessaire ?

Non. Pour une petite application simple, utiliser directement Eloquent peut reduire le nombre de classes et accelerer le developpement. Elle devient pertinente lorsque le projet impose une separation stricte, plusieurs sources de donnees, des tests sans base ou une logique de requetes importante.

#### 4. Quel avantage apporte-t-elle ?

Elle respecte l'inversion de dependance, limite le couplage a Eloquent et rend les services testables sans MySQL. Elle fournit aussi un emplacement unique pour les filtres, les relations chargees et la regle de recherche de conflit.

## Etape 8 : services metier

`CreerReservationService` porte les regles de reservation : salle existante et active, debut avant fin, duree maximale de quatre heures, debut dans le futur et absence de conflit. `AnnulerReservationService` retrouve une reservation et delegue son annulation au repository.

Les services dependent des interfaces de repositories et recoivent leurs dependances par constructeur. Ils ne connaissent ni `$_POST`, ni FastRoute, ni les vues, ni le conteneur. Les exceptions `SalleIndisponibleException`, `ReservationIntrouvableException` et `ReservationInvalideException` expriment les erreurs metier sans les melanger aux details HTTP.

Les tests utilisent des repositories en memoire et n'ont pas besoin de MySQL. Placer ces regles dans un controleur serait plus direct, mais rendrait le comportement difficile a reutiliser et a tester. Les placer dans un modele Eloquent couplerait davantage le metier a la base de donnees.

### Reponses aux questions de l'etape 8

#### 1. Pourquoi ces regles ne sont-elles pas dans le controleur ?

Le controleur orchestre la requete HTTP, la validation, la construction du DTO et la reponse. Les regles de disponibilite doivent rester reutilisables depuis une commande, une API ou un test, donc elles appartiennent au service metier.

#### 2. Pourquoi le service depend-il d'une interface de Repository ?

Une interface permet au service de demander les donnees sans connaitre Eloquent. Cela respecte l'inversion de dependance et permet d'utiliser un repository en memoire ou un mock dans les tests unitaires.

#### 3. Quelle exception doit etre levee en cas de conflit ?

`SalleIndisponibleException` est levee lorsque la salle n'existe pas, est inactive ou possede deja une reservation confirmee qui chevauche la periode demandee. Une reservation annulee ne provoque pas cette exception.

#### 4. Comment tester le service sans MySQL ?

Il faut fournir des implementations en memoire des interfaces de repositories. Les tests de cette etape utilisent `InMemorySalleRepository` et `InMemoryReservationRepository`, ce qui permet de verifier les regles avec des donnees controlees sans ouvrir de connexion MySQL.

## Etape 9 : controleurs et vues

`SalleController` et `ReservationController` orchestrent les requetes web : ils recoivent les donnees, appellent les validateurs, construisent les DTO avec leurs Builders, deleguent au service ou au repository, puis retournent une vue ou une redirection.

Le rendu est centralise dans `ViewRenderer`. Les templates echappent les sorties dynamiques avec `htmlspecialchars` et affichent les erreurs pres des champs concernes. Apres une operation POST reussie, les controleurs retournent une redirection HTTP 303.

Les controleurs ne contiennent aucune requete Eloquent directe et les vues ne connaissent ni le conteneur ni la base de donnees. Mettre le HTML directement dans les controleurs serait plus rapide, mais rendrait l'interface difficile a maintenir. Mettre les regles metier dans les vues produirait aussi des comportements differents selon la page.

### Reponses aux questions de l'etape 9

#### 1. Pourquoi separer les controleurs et les vues ?

Le controleur orchestre le traitement d'une requete, tandis que la vue presente les donnees. Cette separation permet de modifier l'affichage sans modifier les regles de traitement et facilite les tests de chaque couche.

#### 2. Pourquoi echapper les sorties dynamiques ?

`htmlspecialchars` transforme les caracteres speciaux avant leur insertion dans HTML. Cela evite qu'une valeur provenant d'un formulaire ou de la base soit interpretee comme du code HTML ou JavaScript.

#### 3. Pourquoi rediriger apres un POST reussi ?

La redirection applique le pattern Post/Redirect/Get. Elle evite qu'un rafraichissement du navigateur reenvoie le formulaire et cree une nouvelle insertion.

#### 4. Pourquoi afficher les erreurs pres des champs ?

Une erreur associee au nom du champ permet a l'utilisateur de comprendre immediatement quelle valeur corriger. Le tableau d'erreurs conserve aussi les autres erreurs afin de les afficher en une seule fois.

## Etape 10 : routage FastRoute

Les routes sont declarees dans `routes/web.php` sous forme de handlers `[ClasseController::class, 'action']`. `App\Http\Router` construit le dispatcher FastRoute, retire la query string avant le dispatch et demande au conteneur de resoudre le controleur uniquement lorsqu'une route correspond.

Le routeur gere les trois situations attendues : 404 pour un chemin inconnu, 405 pour une methode non autorisee avec l'en-tete `Allow`, et 200/303 selon la reponse du controleur. Les identifiants sont contraints par `\\d+` afin de ne transmettre que des nombres aux actions concernees.

Construire les controleurs directement dans `routes/web.php` serait plus simple, mais contournerait l'injection de dependances. Comparer les URL avec des `if` manuels fonctionnerait pour quelques routes, mais deviendrait fragile et ne gererait pas proprement les methodes interdites.

### Reponses aux questions de l'etape 10

#### 1. Pourquoi FastRoute ne construit-il pas lui-meme le controleur ?

FastRoute a pour responsabilite d'associer une methode et un chemin a un handler. La construction du controleur appartient au conteneur d'injection, qui connait les dependances de la classe. Cette separation respecte la responsabilite unique.

#### 2. Quelle difference existe entre 404 et 405 ?

Une reponse 404 signifie qu'aucune route ne correspond au chemin demande. Une reponse 405 signifie que le chemin existe, mais que la methode HTTP utilisee n'est pas autorisee. Dans ce dernier cas, l'en-tete `Allow` indique les methodes acceptes.

#### 3. Pourquoi contraindre `{id}` avec `\\d+` ?

La contrainte `\\d+` limite le parametre aux chiffres. Une URL comme `/salles/abc` est donc rejetee par le routeur au lieu d'arriver dans une action qui attend un identifiant entier.

#### 4. Quel composant doit interpreter le handler retourne ?

Le dispatcher retourne le handler, puis le routeur demande au conteneur de construire le controleur et appelle l'action avec les parametres dynamiques. Le routeur joue donc le role d'adaptateur entre FastRoute, le conteneur et les controleurs.

## Etape 11 : conteneur PHP-DI

`config/container.php` configure PHP-DI avec l'autowiring pour les classes concretes et des definitions explicites pour les interfaces de repositories. Des factories sont utilisees pour `Capsule\\Manager`, `ViewRenderer` et `Router`, car ces objets ont besoin d'une configuration externe.

`public/index.php` est le seul endroit qui construit le conteneur et demande `Application`. Les autres classes recoivent leurs dependances par constructeur et ne connaissent pas `ContainerInterface` pour rechercher leurs propres objets.

Le conteneur initialise Eloquent en injectant `Capsule\\Manager` dans `Application`, puis le Front Controller lance le routeur avec la methode, l'URI et les donnees POST. Cette approche evite le Service Locator et respecte l'inversion de controle.

### Reponses aux questions de l'etape 11

#### 1. Quelle difference existe entre injection et conteneur ?

L'injection consiste a fournir directement une dependance a une classe, generalement par son constructeur. Le conteneur est l'outil qui construit les objets et assemble automatiquement ces dependances. Une classe doit recevoir ses dependances, pas interroger elle-meme le conteneur.

#### 2. Qu'est-ce que l'autowiring ?

L'autowiring permet a PHP-DI d'inspecter le constructeur d'une classe concrete et de resoudre automatiquement ses parametres. Il evite les definitions repetitives, mais les interfaces et les valeurs scalaires necessitent des definitions explicites.

#### 3. Pourquoi les interfaces necessitent-elles une definition ?

Une interface ne peut pas etre instanciee directement. PHP-DI doit donc savoir quelle implementation utiliser, par exemple `SalleRepositoryInterface` vers `EloquentSalleRepository`.

#### 4. Pourquoi limiter `$container->get()` au point d'entree ?

Limiter l'acces direct au conteneur a `public/index.php` garde les dependances visibles dans les constructeurs. Cela evite le Service Locator, facilite les tests et respecte le principe d'inversion de dependance.

#### 5. Quel anti-pattern apparait si toutes les classes interrogent le conteneur ?

Il s'agit du Service Locator. Les classes deviennent dependantes d'un objet global, leurs dependances sont cachees et leurs tests necessitent un conteneur complet. Cela augmente le couplage et rend l'architecture plus difficile a comprendre.

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
| v0.4.0 | Donnees initiales | Termine |
| v0.5.0 | Validation | Termine |
| v0.6.0 | DTO | Termine |
| v0.7.0 | Repositories | Termine |
| v0.8.0 | Services metier | Termine |
| v0.9.0 | Controleurs et vues | Termine |
| v0.10.0 | Routage | Termine |
| v0.11.0 | Conteneur PHP-DI | Termine |
| v0.12.0 | Tests, dashboard, Docker | Termine |
| v1.0.0 | Version finale | Termine |

## Documentation
Les choix architecturaux, les avantages et les limites des approches retenues seront documentes dans `ARCHITECTURE.md` lors de la finalisation du projet.

## Etape 13 : finalisation

### Messages flash
Les operations reussies (creation de salle, mise a jour, creation de reservation, annulation) affichent un message de succes apres la redirection. Les echecs (reservation introuvable, erreur interne) affichent un message d'erreur. Les messages sont stockes en session par `App\Support\Flash` et affiches une seule fois dans le layout.

### Gestion des exceptions
- Les exceptions metier (`SalleIndisponibleException`, `ReservationInvalideException`, `ReservationIntrouvableException`) sont interceptees dans les controleurs et transformees en erreurs de formulaire ou messages flash.
- Toute exception non interceptee est capturee par `Application::runSafe()` : le detail technique est journalise via `error_log`, l'utilisateur recoit une page 500 dediee avec un message generique.

### Diagramme de classes
Le diagramme de classes complet est disponible dans `docs/diagramme-classes.md` (format PlantUML + Mermaid).

### Lancer l'application
En local :
```bash
php -S localhost:8000 -t public
```
Avec Docker :
```bash
docker compose up -d
```
L'application est accessible sur http://localhost:8000.

### Tests
```bash
./vendor/bin/phpunit
```
Les tests unitaires couvrent la validation et les services ; les tests d'integration utilisent SQLite en memoire pour tester les modeles Eloquent et les repositories sans MySQL.
