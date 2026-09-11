#!/usr/bin/env bash
# =====================================================================
# Sauvegarde / restauration de la base de donnees de l'application.
#
# Utilisation :
#   ./database/backup.sh            -> cree database/backups/<date>.sql.gz
#   ./database/backup.sh restore    -> restore la derniere sauvegarde
#   ./database/backup.sh list       -> liste les sauvegardes
#
# A executer depuis la racine du projet (le conteneur reservation_db
# doit etre en marche).
# =====================================================================
set -euo pipefail

CONTAINER_DB=reservation_db
MYSQL_USER=root
MYSQL_PASSWORD=root
DB_NAME=reservation_salles
BACKUP_DIR="$(cd "$(dirname "$0")" && pwd)/backups"
LATEST=$(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | head -1 || true)

mkdir -p "$BACKUP_DIR"

case "${1:-save}" in
save)
    FILE="$BACKUP_DIR/$(date +%Y%m%d_%H%M%S)_${DB_NAME}.sql.gz"
    docker exec "$CONTAINER_DB" \
        mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$DB_NAME" 2>/dev/null | gzip > "$FILE"
    echo "Sauvegarde creee : $FILE"
    echo "Contenu : $(gunzip -c "$FILE" | grep -c 'INSERT INTO') ordres INSERT"
    ;;

restore)
    if [ -z "$LATEST" ]; then
        echo "Aucune sauvegarde trouvée dans $BACKUP_DIR" >&2
        exit 1
    fi
    echo "Restauration de $LATEST (les donnees actuelles de $DB_NAME seront ecrasees)"
    gunzip -c "$LATEST" | docker exec -i "$CONTAINER_DB" \
        mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$DB_NAME" 2>/dev/null
    echo "Restauration terminee."
    ;;

list)
    ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null || echo "Aucune sauvegarde."
    ;;

*)
    echo "Usage : $0 [save|restore|list]" >&2
    exit 1
    ;;
esac
