## Searchable Json
Тестовое задание

## Требования

- Docker
- Docker Compose

## Установка

1. Клонируйте репозиторий:

   ```bash
   git clone https://github.com/your-repo/project.git
   cd project
   ```

2.	Скопируйте файл .env.example в .env:

    ```bash
    cp .env.example .env
    ```

3. Запустите Sail для установки зависимостей:

    ```bash 
    ./vendor/bin/sail up -d
    ```

4. Установите зависимости Composer:

    ```bash
    ./vendor/bin/sail composer install    
    ```

5. Сгенерируйте ключ приложения:

    ```bash 
   ./vendor/bin/sail artisan key:generate
   ```

6. Выполните миграции базы данных:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```
