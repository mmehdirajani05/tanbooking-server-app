# 🏨 TanBooking - Complete Application Architecture

## 📊 SYSTEM OVERVIEW

TanBooking is a multi-role hotel booking platform with role-based access control, designed to handle:
- Customer hotel bookings via web/mobile apps
- Hotel owner (partner) hotel management
- Admin platform oversight

---

## 👥 USER ROLES & PERMISSIONS

### 1️⃣ **CUSTOMER** (`global_role = 'customer'`)
**Who they are:** Regular users who book hotels (general public)

**Registration:**
- Self-register via mobile app or web portal
- Public registration endpoint: `POST /api/user/auth/register`
- Must verify email before logging in
- **NOT hotel-specific** - can book ANY approved hotel in the system

**Permissions:**
- ✅ Search and view approved hotels
- ✅ Create bookings for themselves
- ✅ View their own booking history
- ✅ Cancel their own bookings
- ✅ Start support chats
- ❌ Cannot see other customers' bookings
- ❌ Cannot manage hotels
- ❌ Cannot access admin features

**Key Characteristic:**
- Global access - can book any hotel from any owner
- Bookings are automatically assigned to authenticated user

---

### 2️⃣ **HOTEL OWNER / PARTNER** (`global_role = 'partner'`)
**Who they are:** Hotel owners or managers who list their properties

**Registration:**
- Self-register via partner portal
- Public registration endpoint: `POST /api/user/auth/register`
- Must verify email before logging in
- **HOTEL-SPECIFIC** - can ONLY manage their own hotels

**Permissions:**
- ✅ Create hotels (requires admin approval)
- ✅ Manage their own hotels (CRUD operations)
- ✅ Create and manage room types for their hotels
- ✅ Manage inventory (available rooms per date)
- ✅ View bookings for their hotels ONLY
- ✅ Update booking status (confirm/cancel)
- ✅ View customers who booked at their hotels
- ✅ Create walk-in customers (for phone/in-person bookings)
- ✅ Access support chat
- ❌ Cannot see other partners' hotels
- ❌ Cannot access admin features
- ❌ Cannot book at other partners' hotels (only their own)

**Hotel-Specific Restrictions:**
All hotel queries are filtered by `owner_id = Auth::id()`:
```php
// In HotelService::getOwnerHotels()
Hotel::where('owner_id', Auth::id())->get();
```

---

### 3️⃣ **ADMIN** (`global_role = 'admin'`)
**Who they are:** Platform administrators who manage the entire system

**Registration:**
- ❌ **CANNOT self-register** (security measure)
- ✅ Created via database seeders
- ✅ Created by existing admins via admin panel
- Endpoint: `POST /api/admin/users` (requires admin auth)

**Permissions:**
- ✅ View ALL hotels (pending, approved, rejected)
- ✅ Approve/reject hotel submissions
- ✅ View ALL bookings across all hotels
- ✅ View ALL customers and partners
- ✅ Create/edit/deactivate users (customers, partners, new admins)
- ✅ Access dashboard with platform-wide statistics
- ✅ Manage support chats
- ✅ Full system oversight

**Key Characteristic:**
- NOT hotel-specific - global access to everything
- Can create staff members (partners, customers, other admins)

---

## 🔄 REGISTRATION & AUTHENTICATION FLOW

### **Customer Registration Flow:**
```
1. User registers via app/web
   POST /api/user/auth/register
   {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "password123",
     "password_confirmation": "password123",
     "global_role": "customer"
   }

2. System sends OTP (currently hardcoded for development)
   - Stored in user_otps table
   - Type: email_verification

3. User verifies email
   POST /api/user/auth/verify-email
   {
     "email": "john@example.com",
     "otp": "123456"
   }

4. After verification, user receives auth token

5. User can now login and book hotels
   POST /api/user/auth/login
```

### **Hotel Owner (Partner) Registration Flow:**
```
1. Partner registers
   POST /api/user/auth/register
   {
     "name": "Rajesh Kumar",
     "email": "rajesh@hotel.com",
     "password": "password123",
     "global_role": "partner"
   }

2. Verify email (same as customer)

3. Login and start adding hotels
```

### **Admin Account Creation:**
```
Option 1: Via Seeder (Development)
   php artisan db:seed --class=DemoDataSeeder

Option 2: Via Admin Panel (Production)
   POST /api/admin/users (requires admin authentication)
   {
     "name": "New Admin",
     "email": "admin2@tanbooking.com",
     "password": "admin123",
     "global_role": "admin"
   }
```

---

## 🏗️ COMPLETE ARCHITECTURE

### **Layer 1: User Registration (App Level)**
```
Mobile App / Web Portal
    ↓
POST /api/user/auth/register
    ↓
AuthService::register()
    - Creates user in database
    - Sends OTP for email verification
    - global_role: 'customer' or 'partner' ONLY
    - Admin registration blocked for security
    ↓
POST /api/user/auth/verify-email
    - Verifies OTP
    - Returns auth token
    ↓
User can now login
```

### **Layer 2: Customer Booking Flow**
```
Customer Login
    ↓
Search Hotels (Public - No Auth Required)
POST /api/customer/hotels/search
    - Shows ONLY approved hotels
    - Shows ONLY available room types
    - Filters by date, city, area
    ↓
Select Hotel & Room Type
    ↓
Create Booking
POST /api/customer/bookings
    - customer_id auto-filled from Auth::id()
    - Checks room availability
    - Locks inventory (prevents double booking)
    - Creates booking with status='pending'
    ↓
Hotel Owner Reviews Booking
GET /hotel/{hotelId}/bookings
    ↓
Hotel Owner Confirms/Rejects
PUT /hotel/{hotelId}/bookings/{id}/status
    - status: 'confirmed' or 'cancelled'
```

### **Layer 3: Hotel Owner Management Flow**
```
Partner Login
    ↓
Create Hotel
POST /api/hotel/create
    - status='pending' (awaiting admin approval)
    - Includes images, amenities, etc.
    ↓
Admin Reviews Hotel
GET /api/admin/hotels/pending
    ↓
Admin Approves
POST /api/admin/hotels/{id}/approve
    - status='approved'
    - Now visible to customers
    ↓
Partner Adds Room Types
POST /api/hotel/{hotelId}/rooms
    ↓
Partner Sets Inventory
POST /api/hotel/{hotelId}/inventory/room/{roomTypeId}/bulk
    ↓
Partner Manages Bookings
GET /api/hotel/{hotelId}/bookings
    - Sees ONLY bookings for their hotel
```

### **Layer 4: Admin Oversight Flow**
```
Admin Login
    ↓
Dashboard
GET /api/admin/dashboard
    - Platform statistics
    ↓
Review Pending Hotels
GET /api/admin/hotels/pending
    ↓
Approve/Reject Hotels
POST /api/admin/hotels/{id}/approve
POST /api/admin/hotels/{id}/reject
    ↓
View All Bookings
GET /api/admin/bookings
    - Across all hotels
    - Filter by status, hotel, date
    ↓
Manage Users
GET /api/admin/users
POST /api/admin/users (create new users)
PUT /api/admin/users/{id} (update users)
POST /api/admin/users/{id}/toggle (activate/deactivate)
```

---

## 📡 COMPLETE API ENDPOINTS

### **🔐 AUTHENTICATION (Public)**

| Method | Endpoint | Access | Description |
|--------|----------|--------|-------------|
| POST | `/api/user/auth/register` | Public | Register new user (customer/partner only) |
| POST | `/api/user/auth/verify-email` | Public | Verify email with OTP |
| POST | `/api/user/auth/login` | Public | Login and get token |
| POST | `/api/user/auth/logout` | Auth | Logout (revoke token) |
| GET | `/api/user/auth/me` | Auth | Get current user info |
| POST | `/api/user/auth/forgot-password` | Public | Request password reset |
| POST | `/api/user/auth/reset-password` | Public | Reset password with OTP |

---

### **👤 CUSTOMER APIs (Requires `role:customer`)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| **Hotels (Public Search)** |
| POST | `/api/customer/hotels/search` | Search approved hotels with availability |
| **Bookings** |
| GET | `/api/customer/bookings` | List my bookings |
| POST | `/api/customer/bookings` | Create a booking |
| GET | `/api/customer/bookings/{id}` | View booking details |
| POST | `/api/customer/bookings/{id}/cancel` | Cancel a booking |
| **Support** |
| POST | `/api/customer/chats/start` | Start support chat |
| GET | `/api/customer/chats` | List my conversations |
| GET | `/api/customer/chats/{id}` | View conversation |
| POST | `/api/customer/chats/{conversationId}/message` | Send message |

---

### **🏨 HOTEL OWNER APIs (Requires `role:partner`)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| **Hotel Management** |
| POST | `/api/hotel/create` | Create hotel (with image upload) |
| GET | `/api/hotel/list` | List my hotels |
| GET | `/api/hotel/{id}` | View hotel details |
| PUT | `/api/hotel/{id}` | Update hotel |
| DELETE | `/api/hotel/{id}` | Delete hotel |
| **Room Types** |
| GET | `/api/hotel/{hotelId}/rooms` | List room types |
| POST | `/api/hotel/{hotelId}/rooms` | Create room type |
| GET | `/api/hotel/{hotelId}/rooms/{id}` | View room type |
| PUT | `/api/hotel/{hotelId}/rooms/{id}` | Update room type |
| DELETE | `/api/hotel/{hotelId}/rooms/{id}` | Delete room type |
| **Inventory** |
| GET | `/api/hotel/{hotelId}/inventory` | View inventory |
| PUT | `/api/hotel/{hotelId}/inventory/room/{roomTypeId}` | Update inventory |
| POST | `/api/hotel/{hotelId}/inventory/room/{roomTypeId}/bulk` | Bulk update |
| **Bookings** |
| GET | `/api/hotel/{hotelId}/bookings` | List bookings for hotel |
| GET | `/api/hotel/{hotelId}/bookings/{id}` | View booking details |
| PUT | `/api/hotel/{hotelId}/bookings/{id}/status` | Update booking status |
| GET | `/api/hotel/{hotelId}/bookings/customers/list` | List customers (for dropdown) |
| POST | `/api/hotel/{hotelId}/bookings/customers/walk-in` | Create walk-in customer |
| **Support** |
| GET | `/api/hotel/{hotelId}/chats` | List conversations |
| GET | `/api/hotel/{hotelId}/chats/{id}` | View conversation |
| POST | `/api/hotel/{hotelId}/chats/{conversationId}/reply` | Reply to message |

---

### **👑 ADMIN APIs (Requires `role:admin`)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| **Dashboard** |
| GET | `/api/admin/dashboard` | Platform overview |
| **User Management** |
| GET | `/api/admin/users` | List all users |
| POST | `/api/admin/users` | Create new user |
| GET | `/api/admin/users/{id}` | View user details |
| PUT | `/api/admin/users/{id}` | Update user |
| POST | `/api/admin/users/{id}/toggle` | Activate/deactivate |
| DELETE | `/api/admin/users/{id}` | Delete user |
| **Hotel Management** |
| GET | `/api/admin/hotels` | List all hotels |
| GET | `/api/admin/hotels/pending` | List pending hotels |
| GET | `/api/admin/hotels/{id}` | View hotel details |
| POST | `/api/admin/hotels/{id}/approve` | Approve hotel |
| POST | `/api/admin/hotels/{id}/reject` | Reject hotel |
| **Bookings** |
| GET | `/api/admin/bookings` | List all bookings |
| **Support** |
| GET | `/api/admin/chats` | List all conversations |
| POST | `/api/admin/chats/{conversationId}/assign` | Assign conversation |

---

## 🔒 SECURITY MEASURES

### **1. Role-Based Access Control**
- Middleware: `EnsureRoleIs`
- Checks `global_role` on protected routes
- Returns 403 if role doesn't match

### **2. Hotel Ownership Verification**
```php
// In HotelService
Hotel::where('owner_id', Auth::id())->get();

// Prevents partners from accessing other partners' hotels
```

### **3. Admin Registration Prevention**
```php
// In AuthService::register()
if ($data['global_role'] === 'admin') {
    throw ValidationException::withMessages([
        'global_role' => ['Admin accounts can only be created by existing administrators.'],
    ]);
}
```

### **4. Booking Ownership**
- Customers can only see their own bookings
- Hotel owners can only see bookings for their hotels
- Admins can see all bookings

### **5. Inventory Locking**
- Database row-level locking prevents double booking
- Uses `lockForUpdate()` during booking creation

---

## 📸 IMAGE UPLOAD SYSTEM

### **Hotel Images:**
- **Upload Method:** `multipart/form-data`
- **Endpoint:** `POST /api/hotel/create` or `PUT /api/hotel/{id}`
- **Field Name:** `images[]` (array of files)
- **Validation:**
  - Format: JPEG, PNG, JPG, GIF, WEBP
  - Max Size: 2MB per image
  - Multiple images allowed
- **Storage:**
  - Path: `storage/app/public/hotels/`
  - URL: `/storage/hotels/filename.jpg`
  - Accessible via: `Storage::url($path)`

### **Room Type Images:**
- **Method:** URL strings (not file uploads)
- **Stored as:** JSON array in database
- **Example:** `["https://example.com/image1.jpg"]`

---

## 🎯 CUSTOMER SELECTION IN BOOKINGS

### **Scenario 1: Customer Self-Booking**
```json
POST /api/customer/bookings
{
  "hotel_id": 1,
  "room_type_id": 1,
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "+91 9876543210",
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_rooms": 2,
  "number_of_guests": 3
}
```
**Result:** `customer_id` auto-filled from authenticated user

---

### **Scenario 2: Hotel Owner Creates Booking for Existing Customer**

**Step 1: Get Customer List**
```json
GET /api/hotel/{hotelId}/bookings/customers/list?search=john
```

**Step 2: Create Booking**
```json
POST /api/hotel/{hotelId}/bookings
{
  "customer_id": 5,  // Existing customer
  "room_type_id": 1,
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "+91 9876543210",
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_rooms": 1,
  "number_of_guests": 2
}
```

---

### **Scenario 3: Hotel Owner Creates Walk-in Customer**

**Step 1: Create Walk-in Customer**
```json
POST /api/hotel/{hotelId}/bookings/customers/walk-in
{
  "name": "Walk-in Guest",
  "phone": "+91 9876543210"
}
```
**Response:**
```json
{
  "success": true,
  "data": {
    "id": 25,
    "name": "Walk-in Guest",
    "email": "walkin_6a8b3c@tanbooking.com",
    "phone": "+91 9876543210"
  }
}
```

**Step 2: Create Booking**
```json
POST /api/hotel/{hotelId}/bookings
{
  "customer_id": 25,  // Walk-in customer
  "room_type_id": 1,
  "guest_name": "Walk-in Guest",
  "guest_email": "walkin_6a8b3c@tanbooking.com",
  "guest_phone": "+91 9876543210",
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-21",
  "number_of_rooms": 1,
  "number_of_guests": 1
}
```

---

## 📊 DATABASE SCHEMA OVERVIEW

### **Key Tables:**

**users**
- `id`, `name`, `email`, `phone`, `password`, `global_role`
- `is_active`, `email_verified_at`, `registration_source`

**hotels**
- `id`, `owner_id` → users
- `name`, `city`, `area`, `address`, `amenities`, `images` (JSON)
- `status` (pending/approved/rejected)

**room_types**
- `id`, `hotel_id` → hotels
- `name`, `max_occupancy`, `price_per_night`, `is_active`

**inventories**
- `id`, `room_type_id` → room_types
- `date`, `total_rooms`, `available_rooms`

**bookings**
- `id`, `customer_id` → users
- `hotel_id` → hotels, `room_type_id` → room_types
- `booking_reference`, `check_in_date`, `check_out_date`
- `status` (pending/confirmed/cancelled)

---

## 🚀 DEPLOYMENT CHECKLIST

### **Pre-Launch:**
1. ✅ Run migrations: `php artisan migrate`
2. ✅ Seed admin: `php artisan db:seed --class=DemoDataSeeder`
3. ✅ Create storage link: `php artisan storage:link`
4. ✅ Set up cron job for inventory management
5. ✅ Configure email service for OTP
6. ✅ Set `APP_ENV=production`
7. ✅ Generate `APP_KEY`

### **Security:**
- ✅ Admin self-registration blocked
- ✅ Role-based middleware on all routes
- ✅ Hotel ownership verification
- ✅ Inventory row locking
- ✅ Password hashing

---

## 📝 IMPORTANT NOTES

### **What's NOT Hotel-Specific:**
- ✅ Customer accounts (global - can book any hotel)
- ✅ Admin access (global - can see everything)

### **What IS Hotel-Specific:**
- ✅ Hotel owner access (only their hotels)
- ✅ Hotel bookings (only for that hotel)
- ✅ Hotel inventory (only for that hotel's rooms)

### **Booking Flow Summary:**
1. **Customer books** → Auto-assigned to authenticated user
2. **Hotel owner books** → Can select existing customer OR create walk-in
3. **Admin books** → Can select any customer

### **Image Upload Summary:**
1. **Hotels** → File upload (multipart/form-data)
2. **Room Types** → URL strings only

---

## 🧪 TESTING CREDENTIALS

After running `php artisan db:seed --class=DemoDataSeeder`:

```
👑 ADMIN:
   Email: admin@tanbooking.com
   Password: admin123

🏨 HOTEL OWNERS:
   Email: rajesh@hotels.com
   Password: partner123
   
   Email: priya@luxuryhotels.com
   Password: partner123

👤 CUSTOMERS:
   Email: amit@example.com
   Password: customer123
   
   Email: sneha@example.com
   Password: customer123
```

---

## 🎓 QUICK REFERENCE

### **Who Can Do What:**

| Action | Customer | Hotel Owner | Admin |
|--------|----------|-------------|-------|
| Book Hotels | ✅ Own bookings only | ✅ For their hotels | ✅ Any hotel |
| View Hotels | ✅ Approved only | ✅ Their hotels only | ✅ All hotels |
| Manage Hotels | ❌ | ✅ Their hotels | ✅ Approve/reject |
| View Customers | ❌ | ✅ Their hotel's customers | ✅ All customers |
| Create Customers | ❌ | ✅ Walk-in guests | ✅ Any user |
| View All Bookings | ❌ | ✅ Their hotels only | ✅ All bookings |
| Create Admin Accounts | ❌ | ❌ | ✅ Only admins |

---

**📧 For Support:** admin@tanbooking.com  
**📚 API Version:** v1  
**🔐 Auth Method:** Laravel Sanctum (Bearer Tokens)
