# TanBooking API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
Most endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## 📋 Table of Contents
1. [User Authentication](#1-user-authentication)
2. [Hotel Owner (Partner) APIs](#2-hotel-owner-partner-apis)
3. [Customer APIs](#3-customer-apis)
4. [Admin APIs](#4-admin-apis)
5. [Public APIs](#5-public-apis)

---

## 1. User Authentication

### 1.1 Register
**POST** `/user/auth/register`

Register a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+91 9876543210",
  "password": "password123",
  "password_confirmation": "password123",
  "global_role": "customer" // or "partner"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully.",
  "data": {
    "user": { ... },
    "token": "xxxxx"
  }
}
```

### 1.2 Login
**POST** `/user/auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### 1.3 Logout
**POST** `/user/auth/logout` (Requires Authentication)

### 1.4 Get Current User
**GET** `/user/auth/me` (Requires Authentication)

### 1.5 Verify Email
**POST** `/user/auth/verify-email`

**Request Body:**
```json
{
  "email": "john@example.com",
  "otp": "123456"
}
```

### 1.6 Forgot Password
**POST** `/user/auth/forgot-password`

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

### 1.7 Reset Password
**POST** `/user/auth/reset-password`

**Request Body:**
```json
{
  "email": "john@example.com",
  "otp": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## 2. Hotel Owner (Partner) APIs

All endpoints require authentication with `role:partner`.

### 2.1 Create Hotel
**POST** `/hotel/create` (Requires Authentication: partner)

Create a new hotel with optional image uploads.

**Request (multipart/form-data):**
```
name: "Grand Hotel"
city: "Mumbai"
area: "Bandra"
address: "123 Main Street"
description: "Luxury hotel" (optional)
phone: "+91 9876543210" (optional)
email: "info@grandhotel.com" (optional)
amenities[]: "WiFi"
amenities[]: "Pool"
images[]: [file] (optional, max 2MB each)
check_in_time: "14:00" (optional)
check_out_time: "12:00" (optional)
```

**Response:**
```json
{
  "success": true,
  "message": "Hotel created successfully and is pending approval.",
  "data": {
    "id": 1,
    "name": "Grand Hotel",
    "status": "pending",
    "owner": { ... }
  }
}
```

### 2.2 List My Hotels
**GET** `/hotel/list` (Requires Authentication: partner)

**Query Parameters:**
- `status` (optional): Filter by status (pending, approved, rejected)

**Response:**
```json
{
  "success": true,
  "message": "Hotels retrieved.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Grand Hotel",
        "city": "Mumbai",
        "status": "approved",
        "room_types_count": 3,
        "bookings_count": 10
      }
    ],
    "total": 5
  }
}
```

### 2.3 View Hotel Details
**GET** `/hotel/{id}` (Requires Authentication: partner)

### 2.4 Update Hotel
**PUT** `/hotel/{id}` (Requires Authentication: partner)

Same fields as create. Can add new images.

### 2.5 Delete Hotel
**DELETE** `/hotel/{id}` (Requires Authentication: partner)

---

### 2.6 Room Type Management

#### List Room Types
**GET** `/hotel/{hotelId}/rooms` (Requires Authentication: partner)

#### Create Room Type
**POST** `/hotel/{hotelId}/rooms` (Requires Authentication: partner)

**Request Body:**
```json
{
  "name": "Deluxe Room",
  "description": "Spacious deluxe room",
  "max_occupancy": 3,
  "price_per_night": 5000.00,
  "number_of_beds": 2,
  "amenities": ["TV", "AC", "Mini Bar"],
  "images": ["url1", "url2"],
  "is_active": true
}
```

#### Update Room Type
**PUT** `/hotel/{hotelId}/rooms/{id}` (Requires Authentication: partner)

#### Delete Room Type
**DELETE** `/hotel/{hotelId}/rooms/{id}` (Requires Authentication: partner)

---

### 2.7 Inventory Management

#### View Inventory
**GET** `/hotel/{hotelId}/inventory` (Requires Authentication: partner)

#### Update Room Inventory
**PUT** `/hotel/{hotelId}/inventory/room/{roomTypeId}` (Requires Authentication: partner)

**Request Body:**
```json
{
  "date": "2026-04-15",
  "available_rooms": 10
}
```

#### Bulk Update Inventory
**POST** `/hotel/{hotelId}/inventory/room/{roomTypeId}/bulk` (Requires Authentication: partner)

**Request Body:**
```json
{
  "start_date": "2026-04-15",
  "end_date": "2026-04-30",
  "available_rooms": 10
}
```

---

### 2.8 Booking Management (Hotel Owner)

#### List Bookings for Hotel
**GET** `/hotel/{hotelId}/bookings` (Requires Authentication: partner)

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, cancelled)

**Response:**
```json
{
  "success": true,
  "message": "Bookings retrieved.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_reference": "TBE6ABC89294",
        "guest_name": "Amit Patel",
        "check_in_date": "2026-04-18",
        "check_out_date": "2026-04-21",
        "status": "pending",
        "total_price": 33000.00,
        "customer": { ... }
      }
    ]
  }
}
```

#### View Booking Details
**GET** `/hotel/{hotelId}/bookings/{id}` (Requires Authentication: partner)

#### Update Booking Status
**PUT** `/hotel/{hotelId}/bookings/{id}/status` (Requires Authentication: partner)

**Request Body:**
```json
{
  "status": "confirmed" // or "cancelled"
}
```

**Note:** This will only show hotels owned by the authenticated partner. The system automatically filters hotels by `owner_id`.

---

## 3. Customer APIs

### 3.1 Search Hotels (Public)
**POST** `/customer/hotels/search`

Search for available hotels with room types.

**Request Body:**
```json
{
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_guests": 2,
  "city": "Mumbai", // optional
  "area": "Bandra", // optional
  "search": "luxury" // optional
}
```

**Response:**
```json
{
  "success": true,
  "message": "Search results retrieved.",
  "data": [
    {
      "id": 1,
      "name": "Grand Mumbai Palace",
      "city": "Mumbai",
      "room_types": [
        {
          "id": 1,
          "name": "Deluxe Sea View",
          "price_per_night": 5500.00,
          "available_rooms": 12,
          "total_price": 33000.00
        }
      ]
    }
  ]
}
```

**Important:** This endpoint only shows **approved hotels** with **available room types** for the selected dates.

---

### 3.2 Customer Bookings (Requires Authentication: customer)

#### List My Bookings
**GET** `/customer/bookings` (Requires Authentication: customer)

**Query Parameters:**
- `status` (optional): Filter by status

**Response:**
```json
{
  "success": true,
  "message": "Bookings retrieved.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_reference": "TBE6ABC89294",
        "hotel": {
          "name": "Grand Mumbai Palace",
          "city": "Mumbai"
        },
        "room_type": {
          "name": "Deluxe Sea View"
        },
        "status": "confirmed"
      }
    ]
  }
}
```

#### Create Booking
**POST** `/customer/bookings` (Requires Authentication: customer)

**Request Body:**
```json
{
  "guest_name": "Amit Patel",
  "guest_email": "amit@example.com",
  "guest_phone": "+91 9876543210",
  "hotel_id": 1,
  "room_type_id": 1,
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_rooms": 2,
  "number_of_guests": 3,
  "notes": "Anniversary celebration"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Booking created successfully.",
  "data": {
    "booking_reference": "TBXXXXXXX",
    "hotel": { "name": "Grand Mumbai Palace" },
    "room_type": { "name": "Deluxe Sea View" },
    "total_price": 33000.00,
    "status": "pending"
  }
}
```

**Important Notes:**
- The `hotel_id` must be an **approved hotel**
- The `room_type_id` must belong to the selected hotel
- The system will check **availability** for the selected dates
- Inventory will be **locked** during booking to prevent double booking

#### View Booking Details
**GET** `/customer/bookings/{id}` (Requires Authentication: customer)

#### Cancel Booking
**POST** `/customer/bookings/{id}/cancel` (Requires Authentication: customer)

---

## 4. Admin APIs

All endpoints require authentication with `role:admin`.

### 4.1 Dashboard
**GET** `/admin/dashboard`

Get overview statistics.

---

### 4.2 Hotel Management

#### List All Hotels
**GET** `/admin/hotels` (Requires Authentication: admin)

**Query Parameters:**
- `status` (optional): Filter by status
- `city` (optional): Filter by city
- `search` (optional): Search by name, city, or area

#### List Pending Hotels
**GET** `/admin/hotels/pending` (Requires Authentication: admin)

#### View Hotel Details
**GET** `/admin/hotels/{id}` (Requires Authentication: admin)

#### Approve Hotel
**POST** `/admin/hotels/{id}/approve` (Requires Authentication: admin)

#### Reject Hotel
**POST** `/admin/hotels/{id}/reject` (Requires Authentication: admin)

**Request Body:**
```json
{
  "reason": "Incomplete information" // optional
}
```

---

### 4.3 Booking Management

#### List All Bookings
**GET** `/admin/bookings` (Requires Authentication: admin)

**Query Parameters:**
- `status` (optional): Filter by status
- `hotel_id` (optional): Filter by hotel
- `date_from` (optional): Filter by check-in date
- `date_to` (optional): Filter by check-in date

**Note:** Admins can view **all bookings** across all hotels. The "Select Customer" dropdown would use the customer information from the booking's `customer_id` field.

---

## 5. Public APIs

### 5.1 Health Check
**GET** `/api/health`

**Response:**
```json
{
  "status": "ok"
}
```

---

## 🔒 Hotel Dropdown Filtering Explanation

### For Hotel Owners (Partners):
When a hotel owner creates a booking or views hotels, they will **ONLY** see hotels where `owner_id` matches their user ID. This is enforced in:
- `HotelService::getOwnerHotels()` - Filters by `Auth::id()`
- All hotel operations check ownership before allowing access

### For Customers:
When customers search for hotels to book, they will **ONLY** see:
- Hotels with `status = 'approved'`
- Hotels with available room types for the selected dates
- This is enforced in `BookingService::searchHotels()`

### For Admins:
Admins can see **ALL** hotels regardless of ownership.

---

## 👤 Select Customer Dropdown

The "Select Customer" functionality is used in these scenarios:

### 1. **Admin View Bookings** (`GET /admin/bookings`)
- Admins can view all bookings with customer details
- Customer information includes: name, email, phone
- No dropdown needed - customer info is included in booking response

### 2. **Hotel Owner View Bookings** (`GET /hotel/{hotelId}/bookings`)
- Hotel owners see bookings for their hotels with customer details
- Customer information is included in the response

**Current Implementation:** The system doesn't have a dedicated "select customer" dropdown endpoint. Customer selection happens implicitly when:
- Customers create their own bookings (automatically uses authenticated user)
- Admins/hotel owners filter bookings by customer (would need custom implementation)

---

## 📸 Image Upload Support

### Hotel Images:
- **Create Hotel**: Accepts multiple image files via `multipart/form-data`
- **Update Hotel**: Can add more images (appends to existing)
- **Supported formats**: JPEG, PNG, JPG, GIF, WEBP
- **Max size**: 2MB per image
- **Storage**: `storage/app/public/hotels/`
- **URL format**: `/storage/hotels/filename.jpg`

### Room Type Images:
- Accepts image URLs (not file uploads)
- Stored as JSON array in database

---

## 🧪 Test Credentials (After Running Seeder)

```
Admin:
  Email: admin@tanbooking.com
  Password: admin123

Hotel Owners:
  Email: rajesh@hotels.com
  Password: partner123
  
  Email: priya@luxuryhotels.com
  Password: partner123

Customers:
  Email: amit@example.com
  Password: customer123
  
  Email: sneha@example.com
  Password: customer123
```

---

## 📝 Notes

1. **All monetary values** are in INR (₹)
2. **Date format**: `YYYY-MM-DD`
3. **Time format**: `HH:MM` (24-hour)
4. **Booking statuses**: `pending`, `confirmed`, `cancelled`
5. **Hotel statuses**: `pending`, `approved`, `rejected`

---

## 🚀 API Testing Examples

### Example: Create Hotel with Images (using cURL)
```bash
curl -X POST http://localhost:8000/api/hotel/create \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "name=Test Hotel" \
  -F "city=Mumbai" \
  -F "area=Bandra" \
  -F "address=123 Test Street" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg" \
  -F "amenities[]=WiFi" \
  -F "amenities[]=Pool"
```

### Example: Search Hotels
```bash
curl -X POST http://localhost:8000/api/customer/hotels/search \
  -H "Content-Type: application/json" \
  -d '{
    "check_in_date": "2026-04-20",
    "check_out_date": "2026-04-23",
    "number_of_guests": 2,
    "city": "Mumbai"
  }'
```

### Example: Create Booking
```bash
curl -X POST http://localhost:8000/api/customer/bookings \
  -H "Authorization: Bearer CUSTOMER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guest_name": "Amit Patel",
    "guest_email": "amit@example.com",
    "guest_phone": "+91 9876543210",
    "hotel_id": 1,
    "room_type_id": 1,
    "check_in_date": "2026-04-20",
    "check_out_date": "2026-04-23",
    "number_of_rooms": 2,
    "number_of_guests": 3
  }'
```
