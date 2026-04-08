# 🏨 TanBooking Server - Configuration & Setup Guide

## 📋 TABLE OF CONTENTS

1. [System Requirements](#system-requirements)
2. [Database Configuration](#database-configuration)
3. [Initial Setup](#initial-setup)
4. [Running the Application](#running-the-application)
5. [Test Credentials](#test-credentials)
6. [API Documentation](#api-documentation)
7. [Troubleshooting](#troubleshooting)

---

## 💻 SYSTEM REQUIREMENTS

### **Required Software:**

- ✅ **PHP 8.2+** (with extensions: openssl, pdo_mysql, mbstring, xml, curl, zip)
- ✅ **Composer** (PHP dependency manager)
- ✅ **MySQL 8.0+** or **MariaDB 10.4+** (XAMPP recommended for Windows)
- ✅ **Git** (version control)

### **Recommended for Windows:**

- **XAMPP** - Includes MySQL, PHP, and Apache
  - Download: https://www.apachefriends.org/
  - MySQL runs on port 3306 by default

---

## 🗄️ DATABASE CONFIGURATION

### **Database Details:**

```
Database Type: MySQL / MariaDB
Host:          127.0.0.1
Port:          3306
Database Name: tanbooking
Username:      root
Password:      (empty - XAMPP default)
```

### **Environment Configuration (.env file):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tanbooking
DB_USERNAME=root
DB_PASSWORD=
```

### **Create Database Manually (if needed):**

```bash
# Using MySQL command line
mysql -u root -e "CREATE DATABASE IF NOT EXISTS tanbooking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

## 🚀 INITIAL SETUP

### **Step 1: Clone the Repository**

```bash
cd C:\Hadi\Projects
git clone <repository-url> tanbooking-server-app
cd tanbooking-server-app
```

### **Step 2: Install Dependencies**

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if needed for frontend)
npm install
```

### **Step 3: Configure Environment**

```bash
# Copy example env file
copy .env.example .env

# Edit .env file with database credentials (see Database Configuration above)
```

### **Step 4: Generate Application Key**

```bash
php artisan key:generate
```

### **Step 5: Run Migrations**

```bash
# Create all database tables
php artisan migrate
```

### **Step 6: Seed Database with Demo Data**

```bash
# Option 1: Seed only (if tables already exist)
php artisan db:seed

# Option 2: Fresh migration + seed (resets everything)
php artisan migrate:fresh --seed

# Option 3: Seed specific seeder
php artisan db:seed --class=DemoDataSeeder
```

**What gets seeded:**
- 1 Admin user
- 2 Hotel owners (partners)
- 4+ Customers
- 4 Hotels (with images & amenities)
- 4 Room Types (with images & amenities)
- 360 Inventory records (90 days for each room type)
- 5 Sample Bookings

### **Step 7: Create Storage Link**

```bash
php artisan storage:link
```

---

## 🏃 RUNNING THE APPLICATION

### **Method 1: Using Start Script (Recommended for Windows)**

```bash
# Double-click or run:
start.bat
```

**What `start.bat` does:**
1. Sets MySQL environment variables
2. Clears all Laravel caches
3. Starts development server on `http://localhost:8000`

### **Method 2: Manual Start**

**Windows (Command Prompt):**
```cmd
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=tanbooking
set DB_USERNAME=root
set DB_PASSWORD=

php artisan serve --host=127.0.0.1 --port=8000
```

**Windows (PowerShell):**
```powershell
$env:DB_CONNECTION="mysql"
$env:DB_HOST="127.0.0.1"
$env:DB_PORT="3306"
$env:DB_DATABASE="tanbooking"
$env:DB_USERNAME="root"
$env:DB_PASSWORD=""

php artisan serve --host=127.0.0.1 --port=8000
```

**Linux/Mac:**
```bash
export DB_CONNECTION=mysql
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_DATABASE=tanbooking
export DB_USERNAME=root
export DB_PASSWORD=

php artisan serve --host=127.0.0.1 --port=8000
```

### **Access the Application:**

```
Main URL:         http://localhost:8000
Admin Panel:      http://localhost:8000/admin/login
API Base URL:     http://localhost:8000/api
Health Check:     http://localhost:8000/api/health
```

---

## 🔐 TEST CREDENTIALS

### **Admin Access:**
```
URL: http://localhost:8000/admin/login
Email: admin@tanbooking.com
Password: admin123
```

### **Hotel Owners (Partners):**
```
Owner 1:
  Email: rajesh@hotels.com
  Password: partner123

Owner 2:
  Email: priya@luxuryhotels.com
  Password: partner123
```

### **Customers:**
```
Customer 1:
  Email: amit@example.com
  Password: customer123

Customer 2:
  Email: sneha@example.com
  Password: customer123

Customer 3:
  Email: vikram@example.com
  Password: customer123

Customer 4:
  Email: ananya@example.com
  Password: customer123
```

---

## 📡 API DOCUMENTATION

### **Base URL:**
```
http://localhost:8000/api
```

### **Authentication:**

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

**Get token by logging in:**
```bash
curl -X POST http://localhost:8000/api/user/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@tanbooking.com","password":"admin123"}'
```

### **Main API Endpoints:**

#### **Authentication (Public):**
```
POST   /api/user/auth/register      - Register new user
POST   /api/user/auth/login         - Login
POST   /api/user/auth/logout        - Logout (auth required)
POST   /api/user/auth/verify-email  - Verify email with OTP
GET    /api/user/auth/me            - Get current user (auth required)
```

#### **Customer APIs (role: customer):**
```
POST   /api/customer/hotels/search          - Search approved hotels
GET    /api/customer/bookings               - List my bookings
POST   /api/customer/bookings               - Create booking
GET    /api/customer/bookings/{id}          - View booking details
POST   /api/customer/bookings/{id}/cancel   - Cancel booking
```

#### **Hotel Owner APIs (role: partner):**
```
POST   /api/hotel/create                  - Create hotel (with images)
GET    /api/hotel/list                    - List my hotels
GET    /api/hotel/{id}                    - View hotel
PUT    /api/hotel/{id}                    - Update hotel
DELETE /api/hotel/{id}                    - Delete hotel

GET    /api/hotel/{hotelId}/rooms         - List room types
POST   /api/hotel/{hotelId}/rooms         - Create room type
PUT    /api/hotel/{hotelId}/rooms/{id}    - Update room type
DELETE /api/hotel/{hotelId}/rooms/{id}    - Delete room type

PUT    /api/hotel/{hotelId}/inventory/room/{roomTypeId}         - Update inventory
POST   /api/hotel/{hotelId}/inventory/room/{roomTypeId}/bulk    - Bulk update

GET    /api/hotel/{hotelId}/bookings                          - List bookings
GET    /api/hotel/{hotelId}/bookings/{id}                     - View booking
PUT    /api/hotel/{hotelId}/bookings/{id}/status              - Update status
GET    /api/hotel/{hotelId}/bookings/customers/list           - List customers
POST   /api/hotel/{hotelId}/bookings/customers/walk-in        - Create walk-in customer
```

#### **Admin APIs (role: admin):**
```
GET    /api/admin/dashboard           - Dashboard overview

GET    /api/admin/users               - List all users
POST   /api/admin/users               - Create user
GET    /api/admin/users/{id}          - View user
PUT    /api/admin/users/{id}          - Update user
POST   /api/admin/users/{id}/toggle   - Activate/deactivate
DELETE /api/admin/users/{id}          - Delete user

GET    /api/admin/hotels              - List all hotels
GET    /api/admin/hotels/pending      - List pending hotels
GET    /api/admin/hotels/{id}         - View hotel
POST   /api/admin/hotels/{id}/approve - Approve hotel
POST   /api/admin/hotels/{id}/reject  - Reject hotel

GET    /api/admin/bookings            - List all bookings
```

### **Full API Documentation:**

See `API_DOCUMENTATION.md` for complete API reference with request/response examples.

---

## 📊 SEEDER DETAILS

### **Available Seeders:**

1. **AdminUserSeeder** - Creates initial admin user
2. **DemoDataSeeder** - Creates complete demo data

### **Run Specific Seeder:**

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=DemoDataSeeder
```

### **Demo Data Includes:**

```
Users:
  - 1 Admin
  - 2 Hotel Owners
  - 4 Customers

Hotels:
  - Grand Mumbai Palace (Approved, Mumbai)
  - Delhi Heritage Hotel (Approved, Delhi)
  - Bangalore Tech Suites (Approved, Bangalore)
  - Chennai Beach Resort (Pending, Chennai)

Room Types:
  - Deluxe Sea View (₹5,500/night)
  - Presidential Suite (₹15,000/night)
  - Heritage Deluxe (₹3,500/night)
  - Business Executive (₹4,000/night)

Inventory:
  - 360 records (90 days × 4 room types)

Bookings:
  - 2 Confirmed
  - 2 Pending
  - 1 Cancelled
```

---

## 🛠️ TROUBLESHOOTING

### **Issue: SQLite error when running commands**

**Solution:**
```bash
# Use start.bat which sets MySQL variables
start.bat

# Or manually set:
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=tanbooking
set DB_USERNAME=root
set DB_PASSWORD=

# Then clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Issue: Database file does not exist**

**Solution:**
Your system has environment variables overriding the `.env` file. Use `start.bat` to ensure correct database connection.

### **Issue: MySQL connection refused**

**Solution:**
1. Open XAMPP Control Panel
2. Start MySQL service
3. Verify MySQL is running on port 3306
4. Try running commands again

### **Issue: Class not found / Missing dependencies**

**Solution:**
```bash
composer dump-autoload
composer install
```

### **Issue: Permission denied on storage**

**Solution (Windows):**
```bash
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T
```

**Solution (Linux/Mac):**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **Issue: 419 Page Expired (CSRF Token)**

This is normal for form submissions. Ensure you're including CSRF token in forms:
```blade
<form method="POST" action="/submit">
    @csrf
    <!-- form fields -->
</form>
```

### **Issue: Routes not found**

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

---

## 📁 PROJECT STRUCTURE

```
tanbooking-server-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── Admin/          - Admin API controllers
│   │   │   ├── Customer/       - Customer API controllers
│   │   │   └── HotelOwner/     - Hotel owner API controllers
│   │   ├── Middleware/         - Custom middleware
│   │   └── Requests/Api/       - Form request validation
│   ├── Models/                 - Eloquent models
│   └── Services/               - Business logic services
├── config/
│   ├── database.php            - Database configuration
│   └── queue.php               - Queue configuration
├── database/
│   ├── factories/              - Model factories for testing
│   ├── migrations/             - Database migrations
│   └── seeders/                - Database seeders
├── routes/
│   ├── api/
│   │   ├── admin.php           - Admin API routes
│   │   ├── customer.php        - Customer API routes
│   │   └── hotel.php           - Hotel owner API routes
│   ├── api.php                 - Main API routes
│   └── web.php                 - Web routes (admin panel)
├── resources/views/admin/      - Admin panel views
├── storage/
│   └── app/public/hotels/      - Uploaded hotel images
├── .env                        - Environment configuration
├── artisan.bat                 - Helper for artisan commands
└── start.bat                   - Application starter
```

---

## 🔧 USEFUL COMMANDS

### **Using artisan.bat:**

```bash
# Database
artisan.bat migrate                 # Run migrations
artisan.bat migrate:rollback        # Rollback last migration
artisan.bat migrate:fresh --seed    # Reset and seed
artisan.bat db:show                 # Show database info
artisan.bat db:table                # List tables

# Cache
artisan.bat cache:clear             # Clear application cache
artisan.bat config:clear            # Clear config cache
artisan.bat view:clear              # Clear compiled views
artisan.bat route:clear             # Clear route cache

# Server
artisan.bat serve                   # Start development server
artisan.bat serve --host=0.0.0.0    # Start on all interfaces

# Code
artisan.bat route:list              # List all routes
artisan.bat optimize                # Optimize application
artisan.bat storage:link            # Create storage symlink
```

---

## 📞 SUPPORT

For issues or questions:
- Check `FINAL_MYSQL_SETUP.md` for MySQL configuration
- Check `API_DOCUMENTATION.md` for API details
- Check `BOOKING_FLOW_GUIDE.md` for booking process
- Check `ARCHITECTURE.md` for system architecture

---

**Happy Coding! 🎉**
