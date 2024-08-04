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

# Затереть любые ручные изменения
git checkout --force ./

# Загрузить последнюю версию приложения
git pull origin master

# Установить зависимости Composer
composer install --no-interaction --optimize-autoloader

# Очистить старый кэш
php artisan cache:clear
php artisan view:clear

# Проверить, что нужно выполнить миграции и сделать дамп
php artisan migrate:check
NEED_MIGRATE="$(echo $?)"
if [[ "$NEED_MIGRATE" != "0" ]];
then
  # Дамп БД
  DATE=$(date '+%Y%m%d_%H%M%S')

  GITHUB_CURRENT_SHA="$(git rev-parse HEAD)"

  DUMP_NAME="../backups/db/${DATE}_${GITHUB_CURRENT_SHA}.sql.gz"

  export $(cat .env | sed 's/#.*//g' | xargs)

  mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE \
      --no-tablespaces \
      --verbose | gzip -c > $DUMP_NAME

  # Запустить миграцию базы данных
  php artisan migrate --force
fi

# Разархивирование билда фронта
PUBLIC="/home/admin/web/artifacts/public-${GITHUB_SHA}.tar.gz"
if [ -f "$PUBLIC" ]; then
    tar xvfz ${PUBLIC} public/
fi

# Закэшировать конфиг
php artisan config:cache

# Обновить права на файлы и директории
chown -R $(id -u):$(id -g) ./
chmod 777 -R storage

# Выход из режима обслуживания
php artisan up

echo "Deployment finished!"
