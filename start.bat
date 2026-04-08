@echo off
REM ============================================
REM TanBooking - MySQL Startup Script
REM ============================================

echo Starting TanBooking with MySQL...
echo.

REM Clear ALL environment variables that might conflict
set DB_CONNECTION=
set DB_HOST=
set DB_PORT=
set DB_DATABASE=
set DB_USERNAME=
set DB_PASSWORD=

REM Set MySQL environment variables
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=tanbooking
set DB_USERNAME=root
set DB_PASSWORD=

REM Navigate to project
cd C:\Hadi\Projects\tanbooking-server-app

REM Clear all Laravel caches
echo Clearing caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan route:clear >nul 2>&1

echo.
echo Starting Laravel development server...
echo Access at: http://localhost:8000
echo Admin Login: http://localhost:8000/admin/login
echo.
echo Credentials:
echo   Email: admin@tanbooking.com
echo   Password: admin123
echo.

REM Start the server
php artisan serve --host=127.0.0.1 --port=8000
