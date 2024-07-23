#!/bin/bash
set -e

echo "Deployment started ..."

cd $PROJECT_PATH

# Проверить, что текущая ветка - мастер
BRANCH="$(git rev-parse --abbrev-ref HEAD)"
if [[ "$BRANCH" != "master" ]]; then
  echo 'Current branch is not master!';
  exit 1;
fi

# Войти в режим обслуживания или вернуть true
# если уже в режиме обслуживания
(php artisan down) || true

# Загрузить последнюю версию приложения
git pull origin master

# Установить зависимости Composer
composer install --optimize-autoloader

# Очистить старый кэш
php artisan cache:clear
php artisan view:clear

# Дамп БД
DATE=$(date '+%Y-%m-%d')

DUMP_NAME="../${DATE_DUMP}-${GITHUB_SHA}-dump.sql"

export $(cat .env | sed 's/#.*//g' | xargs)

export MYSQL_PWD=$DB_PASSWORD

mysqldump -u $DB_USERNAME $DB_DATABASE \
    --no-tablespaces \
    --verbose \
    --result-file $DUMP_NAME

# Запустить миграцию базы данных
php artisan migrate --force

# Закэшировать конфиг
php artisan config:cache

chmod 777 -R storage

# Выход из режима обслуживания
php artisan up

echo "Deployment finished!"
