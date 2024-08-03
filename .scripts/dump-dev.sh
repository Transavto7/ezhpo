cd "$(dirname "$0")"
cd ..
DATE=$(date '+%Y%m%d_%H%M%S')
GITHUB_CURRENT_SHA="$(git rev-parse HEAD)"
DUMP_NAME="${DATE}_${GITHUB_CURRENT_SHA}.sql.gz"
DUMP_PATH="../backups/db/hourly/${DUMP_NAME}"

export $(cat .env | sed 's/#.*//g' | xargs)
export MYSQL_PWD=$DB_PASSWORD
mysqldump -u $DB_USERNAME $DB_DATABASE --no-tablespaces --verbose | gzip -c > $DUMP_PATH

find "../backups/db/hourly" -type f -mmin +1440 -name '*.sql.gz' -execdir rm -- '{}' \;

if [ ! -z "${RESERVE_DUMPS_SERVER}" ]; then
    scp $DUMP_PATH "${RESERVE_DUMPS_SERVER}/hourly/${DB_DATABASE}_${DUMP_NAME}"
fi

HOUR=$(date +%H)
if [ "$HOUR" = "00" ]; then
    DAILY_DUMP_PATH="../backups/db/daily/${DUMP_NAME}"
    cp $DUMP_PATH $DAILY_DUMP_PATH
    find "../backups/db/daily" -type f -mtime +7 -name '*.sql.gz' -execdir rm -- '{}' \;

    if [ ! -z "${RESERVE_DUMPS_SERVER}" ]; then
        scp $DUMP_PATH "${RESERVE_DUMPS_SERVER}/daily/${DB_DATABASE}_${DUMP_NAME}"
    fi
fi
