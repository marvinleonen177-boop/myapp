# Daylight To-Do List

A small Laravel to-do list for practicing routes, controllers, Blade templates, validation, and local JSON file storage. It does not use a database.

## Features

- Add tasks with an optional description
- Edit and delete tasks
- Mark tasks complete or incomplete
- Keep tasks between visits in `storage/app/private/tasks.json`

## Run locally

1. Install the PHP dependencies:

   ```sh
   composer install
   ```

2. Create your environment file and app key (skip the copy if `.env` already exists):

   ```sh
   copy .env.example .env
   php artisan key:generate
   ```

3. Start Laravel:

   ```sh
   php artisan serve
   ```

4. Open the local URL printed by Artisan.

No database setup or migrations are needed. Laravel creates the JSON file when the first task is added. The file is stored on the private local disk and should not be placed in `public/`.

## Run tests

```sh
php artisan test
```
