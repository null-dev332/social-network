# SocSit

A small social network. Laravel 13, Vue 3, Inertia, Tailwind, Breeze, Eloquent, MySQL (SQLite works too for local development).

## Getting started

You need PHP 8.3, Composer, Node and npm.

```
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
php artisan serve
php artisan reverb:start
```

Messages are sent over a websocket (Laravel Reverb, port 8080), so an open conversation updates by itself without refreshing the page. Without `reverb:start` the message is still saved, but the other person only sees it after a refresh.

The default database is SQLite (`database/database.sqlite`), so the app runs without a database server. For MySQL 8 change `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_network
DB_USERNAME=root
DB_PASSWORD=
```

Password reset emails are written to the log (`MAIL_MAILER=log`).

## Accounts after seeding

The password for all of them is `password`.

| Email | Role |
| --- | --- |
| admin@example.com | admin |
| manager@example.com | manager |
| jan@example.com | user |
| petra@example.com | user |
| tomas@example.com | user |

The seeder copies the demo photos from `database/seeders/photos` to `storage/app/public/posts`. The photos are from [Unsplash](https://unsplash.com) (free to use).

## Features

- feed with posts from people you follow, posts can have a photo
- likes (also by double clicking a photo) and comments
- profiles with a post grid, following
- private messages in real time
- searching for people
- admin section

## Layers

- Presentation: `app/Http` (controllers, requests, policies) and `resources/js` (Vue)
- Application: `app/Application/Services`
- Infrastructure: `app/Infrastructure/Repositories`
- Domain: `app/Domain/Models`

A controller calls a service and the service calls a repository, so there is no database code in the controllers. `RepositoryInterface` and `BaseRepository` are the generic part, the specific repositories add the feed, search, following and conversations.

The admin section is at `/admin`. An admin can see everything and change roles, but not the password hash. A manager can moderate posts and comments, but not users or messages.

The custom validation rule is `App\Rules\CleanText` (used for posts, comments, messages and bio). Logging goes through Monolog into `storage/logs/social.log`, channel `social`.

Tests are in the `tests` folder (PHPUnit):

```
php artisan test
```
