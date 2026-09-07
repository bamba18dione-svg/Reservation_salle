# Changelog

Toutes les modifications importantes du projet sont documentees dans ce fichier.

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

## [v0.0.0] - 2026-09-06

### Ajoute

- Initialisation du depot Git.
- Creation de la branche `main`.
- Creation de la documentation initiale du projet.
- Ajout des regles d'exclusion Git.
- Creation de la structure du journal des versions.


#  1.Quel est le rôle de Composer ?

Composer est le gestionnaire de dépendances standard en PHP. Il gère l'installation/mise à jour des bibliothèques tierces et génère l'autoloader PSR-4 pour charger automatiquement les classes sans require manuel.


# 2. Quelle différence existe entre require et require-dev ?

- require : contient les dépendances indispensables au fonctionnement de l'application en production (ex: ORM, Routeur). 

-require-dev : contient les outils de développement et de test (ex: PHPUnit) qui ne doivent pas être installés en production.  


# 3. Pourquoi faut-il versionner composer.lock ?

composer.lock fixe les versions exactes (au commit près) de tous les paquets installés. Le versionner garantit que tous les développeurs et le serveur de production utilisent rigoureusement les mêmes versions des bibliothèques, évitant le problème du "ça marche chez moi".

# 4. Pourquoi ne versionne-t-on pas vendor/ ?

Le dossier vendor/ contient des milliers de fichiers lourds générés automatiquement. Il peut être récréé à tout moment avec la commande composer install grâce aux fichiers composer.json et composer.lock


### ETAPE 2

# 1 Quel rôle joue Capsule\Manager ?

Illuminate\Database\Capsule\Manager agit comme un adaptateur et un conteneur d'amorce (ou wrapper). 
-- Configuration centralisée : Il regroupe la configuration des accès à la base de données (hôte, nom de la base, identifiants, encodage) et initialise le gestionnaire de connexions PDO.

-- Pont statique : En appelant $capsule->setAsGlobal() et $capsule->bootEloquent(), il enregistre le gestionnaire de base de données de manière globale. Cela permet aux classes de modèles (qui héritent de Illuminate\Database\Eloquent\Model) d'accéder aux connexions BDD et d'exécuter des requêtes via des méthodes statiques (ex: Salle::find($id)), exactement comme dans Laravel.

# 2 Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Eloquent peut fonctionner de manière autonome car le framework Laravel a été conçu de façon découplée et modulaire :

--Composants indépendants : L'ORM est publié sous forme d'un composant autonome disponible sur Packagist via le paquet illuminate/database.  

--Absence de dépendances strictes au Framework : Eloquent n'a pas besoin du noyau HTTP de Laravel, de ses contrôleurs ou de son système de vue pour exécuter des requêtes. Il a uniquement besoin d'un gestionnaire de connexion (fourni par Capsule) et d'un résolveur d'événements/paginateur minimal.

# 3 Où doit se trouver le démarrage de l’ORM ?

Le démarrage de l'ORM doit impérativement se trouver dans la phase d'amorçage de l'application (Bootstrap), idéalement dans un fichier de configuration dédié (ex: config/database.php) qui est chargé dès le point d'entrée unique (public/index.php).

#Pourquoi à cet endroit ?

Démarrage unique (Early Initialization) : L'ORM doit être configuré et prêt avant que tout contrôleur, repository ou service métier ne tente d'accéder aux données.

Isolation : Placer le démarrage dans config/database.php évite de dupliquer la configuration de la base de données ou d'instancier plusieurs connexions PDO inutiles au fil de l'exécution.


# 4  Quelle différence existe entre ORM et SQL écrit à la main ?

SQL écrit à la main = tu demandes directement à la base de données quoi faire.

ORM = tu manipules des objets PHP, et l'ORM traduit tes actions en SQL.