chcp 65001

cd /d ..\

php artisan FirebaseNotifCommand:run {--user_id= : push notif  jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak} {--type= : push notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi} {--title= : push notif title} {--text= : push notif text}
