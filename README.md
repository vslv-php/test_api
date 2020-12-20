Для запуска проекта:

1. Запустить docker-контейнер: <br>
```
docker-compose up -d
```

2. Выполнить миграции: <br>
```
docker-compose exec php-fpm php yii migrate --interactive=0
```

3. Добавить тестовых пользователей:
```
docker-compose exec php-fpm php yii test/add-users
```
Будут созданы три пользователя: test@test.com, alice@gmail.com, bob@mail.ru; пароль у всех: 123456

4. Базовый URL API будет ```http://localhost:8800/api/v1/```

<hr>
Документация доступна по адресу: <a href="https://documenter.getpostman.com/view/6262533/TVsuBSWG">https://documenter.getpostman.com/view/6262533/TVsuBSWG</a>