chcp 65001

cd /d ..\

php artisan SMSNotifCommand:run {--user_id= : sms notif jo'natiladigan user id, bunga qiymat kiritilsa type yozilmasligi kerak} {--type= : sms notif jo'natiladigan role, agar role yozilsa user id kiritilmaydi} {--text= : sms text}
