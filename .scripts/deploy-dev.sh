#!/bin/bash
set -e

echo "Deployment started ..."

cd ~/crm.ta-7.ru/public_html

# Проверить, что текущая ветка - дев
BRANCH="$(git rev-parse --abbrev-ref HEAD)"
if [[ "$BRANCH" != "dev" ]]; then
  echo 'Current branch is not dev!';
  exit 1;
fi

PHP_VERSION=php7.3

# Войти в режим обслуживания или вернуть true
# если уже в режиме обслуживания
($PHP_VERSION artisan down) || true

# Загрузить последнюю версию приложения
git pull origin dev

# Установить зависимости Composer
$PHP_VERSION composer.phar install --optimize-autoloader

# Очистить старый кэш
$PHP_VERSION artisan cache:clear
$PHP_VERSION artisan view:clear

# Дамп БД
DATE_DUMP=$(date '+%Y-%m-%d')

DUMP_NAME="../${DATE_DUMP}-${GITHUB_SHA}-dump.sql"

export $(cat .env | sed 's/#.*//g' | xargs)

export MYSQL_PWD=$DB_PASSWORD

mysqldump -u $DB_USERNAME $DB_DATABASE \
    --no-tablespaces \
    --verbose \
    --result-file $DUMP_NAME

# Запустить миграцию базы данных
$PHP_VERSION artisan migrate --force

# Закэшировать конфиг
$PHP_VERSION artisan config:cache

chmod 777 -R storage

# Выход из режима обслуживания
$PHP_VERSION artisan up

echo "Deployment finished!"
