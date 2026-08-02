#!/bin/bash
set -e

# Render بيحدد الـ PORT عن طريق environment variable، لو مش موجود نستخدم 80
PORT="${PORT:-80}"

# استبدال الـ port في إعدادات Apache
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf
sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf

# لو الـ APP_KEY مش موجود، ولّده (مرة واحدة بس مفروض)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# تشغيل الـ migrations تلقائي عند كل deploy (اختياري - ممكن تشيلها لو عايز تشغلها يدوي)
php artisan config:cache
php artisan route:cache

# تشغيل Apache في المقدمة (foreground) عشان الـ container يفضل شغال
apache2-foreground