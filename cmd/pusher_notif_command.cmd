chcp 65001

cd /d ..\

php artisan PusherNotifCommand:run {--user_id= : pusher orqali notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak} {--type= : pusher orqali notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi} {--title= : pusher notif title} {--text= : pusher notif text}

