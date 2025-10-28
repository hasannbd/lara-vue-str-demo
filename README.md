# Laravel + Vue starter kit demo 
Laravel 12 + Vue starter kit with Spatie permission start up demo
Setup Instructions

- Clone git repository:
```
git clone https://github.com/hasannbd/lara-vue-str-demo.git
```
- Copy .env.example to .env and set your database credentials:
- Run commands:
```
cd lara-vue-str-demo
npm install   #Install Node.js dependencies
npm run build   #Build the Vue frontend assets
composer install #Install PHP dependencies
php artisan migrate --seed #Default Role, User and Permission will be created
php artisan key:generate #This will generate application key
composer run dev   #Start the Laravel development server
```
- Visit http://localhost:8000
- Login with:
```
  Username: admin@example.com
  Password: Welcome
```
