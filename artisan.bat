@echo off
REM Set MySQL environment variables
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=tanbooking
set DB_USERNAME=root
set DB_PASSWORD=

REM Navigate to project directory
cd C:\Hadi\Projects\tanbooking-server-app

REM Run the artisan command passed as arguments
php artisan %*
