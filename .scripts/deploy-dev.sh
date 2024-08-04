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

GITHUB_CURRENT_SHA="$(git rev-parse HEAD)"

PHP_VERSION=php7.3

# Войти в режим обслуживания или вернуть true
# если уже в режиме обслуживания
($PHP_VERSION artisan down) || true

# Затереть любые ручные изменения
git checkout --force ./

# Загрузить последнюю версию приложения
git pull origin dev

# Установить зависимости Composer
$PHP_VERSION composer.phar install --no-interaction --optimize-autoloader

# Очистить старый кэш
$PHP_VERSION artisan cache:clear
$PHP_VERSION artisan view:clear

# Проверить, что нужно выполнить миграции и сделать дамп
$PHP_VERSION artisan migrate:check
NEED_MIGRATE="$(echo $?)"
if [[ "$NEED_MIGRATE" != "0" ]];
then
  # Дамп БД
  DATE=$(date '+%Y%m%d_%H%M%S')

  GITHUB_CURRENT_SHA="$(git rev-parse HEAD)"

  DUMP_NAME="../backups/db/${DATE}_${GITHUB_CURRENT_SHA}.sql.gz"

  export $(cat .env | sed 's/#.*//g' | xargs)

  export MYSQL_PWD=$DB_PASSWORD

  /usr/local/bin/mysqldump -u $DB_USERNAME $DB_DATABASE \
      --no-tablespaces \
      --verbose | gzip -c > $DUMP_NAME

  # Запустить миграцию базы данных
  $PHP_VERSION artisan migrate --force
fi

# Разархивирование билда фронта
PUBLIC="../artifacts/public-${GITHUB_SHA}.tar.gz"
if [ -f "$PUBLIC" ]; then
    tar xvfz ${PUBLIC} public/
fi

# Закэшировать конфиг
$PHP_VERSION artisan config:cache

# Обновить права на файлы и директории
chown -R $(id -u):$(id -g) ./
chmod 777 -R storage

# Выход из режима обслуживания
$PHP_VERSION artisan up

echo "Deployment finished!"
