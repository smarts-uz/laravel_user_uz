chcp 65001

cd /d ..\

php artisan EmailNotifCommand:run {--user_id= : email notif jo'natiladigan user id, bunga qiymat kiritilsa type yozilmasligi kerak} {--type= : email notif jo'natiladigan role, agar role yozilsa user id kiritilmaydi} {--text= : email text}
