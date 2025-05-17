Перед запуском 
```bash
docker network create traefik-network && docker network create ehzpo-network
```

 Импорт дефолтной БД руками
```bash
docker-compose exec -T mysql mysql -uuser -psecret ezhpo_registry < template.sql
```


Сбор и пуш в регистри
```bash
docker buildx build --platform linux/amd64,linux/arm64 -t docker.registry.ta-7.ru/ehzpo:0.0.1 -f production/php-fpm/Dockerfile --push .
```
