# Changelog

Toutes les modifications importantes du projet sont documentees dans ce fichier.

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
