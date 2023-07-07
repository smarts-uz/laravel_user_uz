chcp 65001

cd /d ..\

php artisan queue:listen --timeout=0

pause