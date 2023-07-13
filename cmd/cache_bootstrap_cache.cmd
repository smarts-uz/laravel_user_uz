chcp 65001

cd /d ..\

php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan event:clear
php artisan optimize:clear

cd bootstrap
rmdir /S /Q cache
mkdir cache
