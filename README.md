pdaphp artisan key:generate# laravel_voyager_youdo

### Installing steps
#### 1-step
```md
git clone https://github.com/teamprodev/laravel_voyager_youdo.git
cd laravel_voyager_youdo
composer update
copy .env.example .env
php artisan key:generate
```
#### 2-step
#### Next make sure to create a new database and add your database credentials to your .env file, you will also want to add your application URL in the APP_URL variable
```md
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="your DB name"
DB_USERNAME="your DB username"
DB_PASSWORD="your DB password"
```
