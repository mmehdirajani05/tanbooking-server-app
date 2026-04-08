# 🏨 TanBooking - Complete Booking Flow & Inventory Management

## 📋 TABLE OF CONTENTS

1. [Booking Flow Overview](#booking-flow-overview)
2. [Inventory Management System](#inventory-management-system)
3. [Complete Step-by-Step Booking Process](#complete-step-by-step-booking-process)
4. [API Endpoints for Booking](#api-endpoints-for-booking)
5. [Postman Collection Guide](#postman-collection-guide)
6. [Test Scenarios](#test-scenarios)

---

## 🎯 BOOKING FLOW OVERVIEW

### **The Correct Booking Flow:**

```
1. Set Room Inventory (Admin/Hotel Owner)
   ↓
2. User searches for hotels with dates
   ↓
3. System checks availability for those dates
   ↓
4. If available → Allow booking
   ↓
5. If not available → Show error
   ↓
6. Create booking (reduces inventory)
   ↓
7. Hotel owner confirms/cancels booking
   ↓
8. If cancelled → Inventory restored
```

---

## 📊 INVENTORY MANAGEMENT SYSTEM

### **What is Inventory?**

Inventory is the number of rooms available for booking on specific dates. Each room type has its own inventory for each date.

**Example:**
```
Hotel: Grand Mumbai Palace
Room Type: Deluxe Sea View
Total Rooms: 15

Inventory:
- 2026-04-20: 12 available (3 already booked)
- 2026-04-21: 10 available (5 already booked)
- 2026-04-22: 15 available (none booked)
```

### **How Inventory Works:**

1. **Setting Inventory:**
   - Admin or hotel owner sets total rooms and available rooms
   - Can be done for single date or date range
   - Default: Available = Total (nothing booked yet)

2. **Checking Availability:**
   - When user searches with dates, system checks inventory for ALL dates in range
   - Returns MINIMUM available rooms across all dates
   - Example: If 10 rooms on Apr 20, 8 on Apr 21, 12 on Apr 22 → Shows 8 available

3. **Creating Booking:**
   - Reduces inventory for each date in the booking
   - Uses database locking to prevent double-booking
   - Example: Book 2 rooms from Apr 20-23 → Reduces inventory by 2 for each date

4. **Cancelling Booking:**
   - Restores inventory for each date
   - Allows those rooms to be booked again

---

## 🔄 COMPLETE STEP-BY-STEP BOOKING PROCESS

### **STEP 1: Admin Sets Up Hotel**

**Action:** Admin creates hotel with details and images

**Admin Panel:** Hotels → Create Hotel

**What happens:**
- Hotel created with status (pending/approved)
- Images uploaded and stored
- Amenities selected
- Owner assigned

---

### **STEP 2: Admin Adds Room Types**

**Action:** Admin adds room types to hotel

**Admin Panel:** Hotels → View Hotel → Add Room Type

**What happens:**
- Room type created (e.g., "Deluxe Sea View")
- Set pricing, capacity, amenities
- Add room images (URLs)
- Set as active/inactive

**Example Room Type:**
```json
{
  "name": "Deluxe Sea View",
  "max_occupancy": 3,
  "price_per_night": 5500.00,
  "number_of_beds": 2,
  "amenities": ["TV", "AC", "Mini Bar", "Safe"],
  "images": ["https://example.com/room.jpg"],
  "is_active": true
}
```

---

### **STEP 3: Admin Sets Inventory (Room Availability)**

**Action:** Admin sets how many rooms are available for which dates

**Admin Panel:** Hotels → View Hotel → Manage Inventory (for each room type)

**What happens:**
- Select date range (e.g., Apr 1 - Apr 30, 2026)
- Set total rooms (e.g., 15 rooms)
- Set available rooms (e.g., 15 initially)
- System creates inventory records for each date

**Example:**
```
Start Date: 2026-04-01
End Date: 2026-04-30
Total Rooms: 15
Available Rooms: 15

Result: 30 inventory records created (one for each day)
Each day: 15 total, 15 available
```

**Why This is Important:**
- Without inventory, rooms cannot be booked
- System will show "0 rooms available" if no inventory exists
- Inventory is reduced when bookings are made
- Inventory is restored when bookings are cancelled

---

### **STEP 4: Customer Searches for Hotels**

**API Endpoint:** `POST /api/customer/hotels/search`

**Request:**
```json
{
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_guests": 2,
  "city": "Mumbai"
}
```

**What Happens Behind the Scenes:**

1. System finds all approved hotels in Mumbai
2. For each hotel, checks room types that:
   - Are active (`is_active = true`)
   - Can accommodate the guests (`max_occupancy >= 2`)
   - Have inventory for ALL dates (Apr 20, 21, 22)
3. Returns hotels with available room types and their availability

**Response:**
```json
{
  "success": true,
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
          "available_rooms": 12,  // Minimum across all 3 dates
          "total_price": 16500.00  // 5500 × 3 nights
        }
      ]
    }
  ]
}
```

**Important:**
- If a room type has NO inventory records for the dates → NOT shown
- If available_rooms = 0 for any date → NOT shown
- Only rooms with availability are returned

---

### **STEP 5: Customer Creates Booking**

**API Endpoint:** `POST /api/customer/bookings`

**Request:**
```json
{
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "+91 9876543210",
  "hotel_id": 1,
  "room_type_id": 1,
  "check_in_date": "2026-04-20",
  "check_out_date": "2026-04-23",
  "number_of_rooms": 2,
  "number_of_guests": 2,
  "notes": "High floor preferred"
}
```

**What Happens Behind the Scenes:**

1. **Validate Hotel is Approved:**
   ```php
   Hotel::where('id', 1)->where('status', 'approved')->first();
   ```

2. **Validate Room Type Exists and Active:**
   ```php
   RoomType::where('id', 1)
     ->where('hotel_id', 1)
     ->where('is_active', true)->first();
   ```

3. **Check Inventory Availability (with locking):**
   ```
   Check Apr 20: 12 available ≥ 2 requested? ✅
   Check Apr 21: 10 available ≥ 2 requested? ✅
   Check Apr 22: 15 available ≥ 2 requested? ✅
   ```

4. **Calculate Total Price:**
   ```
   Nights = Apr 23 - Apr 20 = 3 nights
   Total = ₹5,500 × 3 nights × 2 rooms = ₹33,000
   ```

5. **Reduce Inventory (in transaction):**
   ```
   Apr 20: 12 → 10 available
   Apr 21: 10 → 8 available
   Apr 22: 15 → 13 available
   ```

6. **Create Booking:**
   ```json
   {
     "booking_reference": "TB12345ABCDE",
     "customer_id": 5,
     "hotel_id": 1,
     "room_type_id": 1,
     "status": "pending",
     "total_price": 33000.00
   }
   ```

**If Inventory Check Fails:**
```json
{
  "errors": {
    "number_of_rooms": ["Only 1 rooms available for 2026-04-21."]
  }
}
```

---

### **STEP 6: Hotel Owner Reviews Booking**

**API Endpoint:** `GET /api/hotel/1/bookings`

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "booking_reference": "TB12345ABCDE",
      "guest_name": "John Doe",
      "status": "pending",
      "check_in_date": "2026-04-20",
      "check_out_date": "2026-04-23",
      "number_of_rooms": 2,
      "total_price": 33000.00,
      "customer": {
        "name": "John Doe",
        "email": "john@example.com"
      }
    }
  ]
}
```

---

### **STEP 7: Hotel Owner Confirms Booking**

**API Endpoint:** `PUT /api/hotel/1/bookings/1/status`

**Request:**
```json
{
  "status": "confirmed"
}
```

**What Happens:**
- Booking status changes from "pending" to "confirmed"
- `confirmed_at` timestamp set
- Inventory already reduced (stays reduced)
- Customer receives confirmation (email/notification if set up)

---

### **STEP 8: What If Booking is Cancelled?**

**By Customer:** `POST /api/customer/bookings/1/cancel`

**By Hotel Owner:** `PUT /api/hotel/1/bookings/1/status` with `"status": "cancelled"`

**What Happens:**
```json
{
  "status": "cancelled",
  "cancelled_at": "2026-04-15T10:30:00.000000Z"
}
```

**Inventory Restored:**
```
Apr 20: 10 → 12 available
Apr 21: 8 → 10 available
Apr 22: 13 → 15 available
```

Now those rooms can be booked again!

---

## 📡 API ENDPOINTS FOR BOOKING

### **Customer Booking Flow:**

```
1. Search Hotels
   POST /api/customer/hotels/search
   Body: { check_in_date, check_out_date, number_of_guests, city }

2. View Available Rooms (returned in search results)
   - Room types with availability
   - Pricing for the date range
   - Number of available rooms

3. Create Booking
   POST /api/customer/bookings
   Body: { hotel_id, room_type_id, check_in_date, check_out_date, ... }

4. View My Bookings
   GET /api/customer/bookings?status=pending

5. Cancel Booking
   POST /api/customer/bookings/{id}/cancel
```

### **Hotel Owner Booking Management:**

```
1. List Customers (for dropdown)
   GET /api/hotel/{hotelId}/bookings/customers/list?search=name

2. Create Walk-in Customer
   POST /api/hotel/{hotelId}/bookings/customers/walk-in
   Body: { name, email, phone }

3. Create Booking for Customer
   POST /api/hotel/{hotelId}/bookings
   Body: { customer_id, room_type_id, check_in_date, ... }

4. View Hotel Bookings
   GET /api/hotel/{hotelId}/bookings?status=pending

5. Update Booking Status
   PUT /api/hotel/{hotelId}/bookings/{id}/status
   Body: { status: "confirmed" or "cancelled" }
```

### **Admin Inventory Management:**

```
1. Set Inventory (Single Date)
   PUT /api/hotel/{hotelId}/inventory/room/{roomTypeId}
   Body: { date, total_rooms, available_rooms }

2. Set Inventory (Bulk - Date Range)
   POST /api/hotel/{hotelId}/inventory/room/{roomTypeId}/bulk
   Body: { start_date, end_date, total_rooms, available_rooms }

3. View Inventory
   GET /api/hotel/{hotelId}/inventory/room/{roomTypeId}/{startDate}/{endDate}
```

---

## 📮 POSTMAN COLLECTION GUIDE

### **How to Import:**

1. Open Postman
2. Click **Import**
3. Select file: `POSTMAN_COLLECTION.json`
4. Collection imported successfully

### **Setup:**

1. **Set Base URL:**
   - Collection Variables → `base_url` = `http://localhost:8000/api`

2. **Login First:**
   - Run: `1. Authentication → Login`
   - Uses: `admin@tanbooking.com` / `admin123`
   - Token automatically saved to `{{access_token}}`

3. **Test in Order:**
   - Authentication → Admin → Hotel Owner → Customer

### **Test Data Included:**

**Admin Credentials (from seeder):**
```
Email: admin@tanbooking.com
Password: admin123
```

**Hotel Owner Credentials:**
```
Email: rajesh@hotels.com
Password: partner123

Email: priya@luxuryhotels.com
Password: partner123
```

**Customer Credentials:**
```
Email: amit@example.com
Password: customer123

Email: sneha@example.com
Password: customer123
```

**Hotels (from seeder):**
```
1. Grand Mumbai Palace (Approved)
   - Room: Deluxe Sea View (ID: 1)
   - Room: Presidential Suite (ID: 2)

2. Delhi Heritage Hotel (Approved)
   - Room: Heritage Deluxe (ID: 3)

3. Bangalore Tech Suites (Approved)
   - Room: Business Executive (ID: 4)

4. Chennai Beach Resort (Pending)
```

---

## 🧪 TEST SCENARIOS

### **Scenario 1: Complete Customer Booking Flow**

**Steps:**

1. **Login as Customer:**
   ```
   POST /api/user/auth/login
   { "email": "amit@example.com", "password": "customer123" }
   ```

2. **Search Hotels:**
   ```
   POST /api/customer/hotels/search
   {
     "check_in_date": "2026-04-20",
     "check_out_date": "2026-04-23",
     "number_of_guests": 2,
     "city": "Mumbai"
   }
   ```
   **Expected:** Grand Mumbai Palace with Deluxe Sea View (12 available)

3. **Create Booking:**
   ```
   POST /api/customer/bookings
   {
     "hotel_id": 1,
     "room_type_id": 1,
     "check_in_date": "2026-04-20",
     "check_out_date": "2026-04-23",
     "number_of_rooms": 2,
     "number_of_guests": 2,
     "guest_name": "Amit Patel",
     "guest_email": "amit@example.com",
     "guest_phone": "+91 9876543213"
   }
   ```
   **Expected:** Booking created, inventory reduced by 2

4. **Check Availability Again:**
   ```
   POST /api/customer/hotels/search (same as step 2)
   ```
   **Expected:** Now shows 10 available (was 12, booked 2)

5. **View My Bookings:**
   ```
   GET /api/customer/bookings
   ```
   **Expected:** Shows new booking with status "pending"

---

### **Scenario 2: Hotel Owner Confirms Booking**

**Steps:**

1. **Login as Hotel Owner:**
   ```
   POST /api/user/auth/login
   { "email": "rajesh@hotels.com", "password": "partner123" }
   ```

2. **View Bookings:**
   ```
   GET /api/hotel/1/bookings
   ```
   **Expected:** See pending bookings for Grand Mumbai Palace

3. **Confirm Booking:**
   ```
   PUT /api/hotel/1/bookings/1/status
   { "status": "confirmed" }
   ```
   **Expected:** Status changes to "confirmed"

---

### **Scenario 3: Booking Fails Due to No Inventory**

**Setup:** Check all inventory for specific date

1. **Try to Book Beyond Availability:**
   ```
   POST /api/customer/bookings
   {
     "hotel_id": 1,
     "room_type_id": 1,
     "check_in_date": "2026-04-20",
     "check_out_date": "2026-04-23",
     "number_of_rooms": 15,  // More than available
     "number_of_guests": 2,
     "guest_name": "Test User",
     "guest_email": "test@example.com",
     "guest_phone": "+91 9999999999"
   }
   ```
   **Expected Error:**
   ```json
   {
     "errors": {
       "number_of_rooms": ["Only 10 rooms available for 2026-04-21."]
     }
   }
   ```

---

### **Scenario 4: Admin Sets Up New Hotel from Scratch**

**Complete Flow:**

1. **Login as Admin**
   - Run: `Login` endpoint

2. **Create User (Partner)**
   ```
   POST /api/admin/users
   {
     "name": "Test Hotel Owner",
     "email": "testowner@example.com",
     "password": "password123",
     "global_role": "partner"
   }
   ```

3. **Login as New Partner**
   - Use new credentials to login

4. **Create Hotel (with images)**
   ```
   POST /api/hotel/create (multipart/form-data)
   - Add hotel details
   - Upload images
   ```

5. **Login as Admin**

6. **Approve Hotel**
   ```
   POST /api/admin/hotels/{hotelId}/approve
   ```

7. **Add Room Types (via Admin Panel)**
   - Go to hotel detail page
   - Click "Add Room Type"
   - Fill room details

8. **Set Inventory (via Admin Panel)**
   - Click "Manage Inventory" for room type
   - Set date range and room count

9. **Now customers can search and book!**

---

## ⚠️ IMPORTANT NOTES

### **Inventory Rules:**

1. **No Inventory = No Booking**
   - If no inventory records exist for dates → Booking fails
   - System shows "0 rooms available"

2. **Minimum Across Dates**
   - Available rooms = MINIMUM across all dates in range
   - Example: 10 on Apr 20, 5 on Apr 21, 8 on Apr 22 → Shows 5 available

3. **Automatic Reduction**
   - When booking created → Inventory reduced
   - When booking cancelled → Inventory restored
   - Uses database locking to prevent double-booking

4. **Setting Inventory**
   - Should be done BEFORE customers can book
   - Can set 90-365 days in advance
   - Bulk update available for date ranges

### **Booking Validation Order:**

```
1. Hotel must be approved
2. Room type must exist and be active
3. Room type must belong to hotel
4. Guests must not exceed occupancy
5. Inventory must exist for all dates
6. Available rooms must be sufficient
7. Check-in date must be today or future
8. Check-out must be after check-in
```

### **Admin Panel Inventory Flow:**

```
Admin Panel → Hotels → View Hotel → Room Types
                                           ↓
                              Click "Manage Inventory"
                                           ↓
                              Set: Start Date, End Date, Total Rooms, Available
                                           ↓
                              System creates inventory for each date
```

---

## 🎯 QUICK REFERENCE

### **Booking Availability Check:**

**Before Booking:**
```
User selects: Apr 20-23, 2 rooms
System checks:
  - Apr 20: 12 available ≥ 2? ✅
  - Apr 21: 10 available ≥ 2? ✅
  - Apr 22: 15 available ≥ 2? ✅
Result: CAN BOOK
```

**After Booking:**
```
Booked 2 rooms
Inventory now:
  - Apr 20: 10 available
  - Apr 21: 8 available
  - Apr 22: 13 available
```

**If Cancelled:**
```
Inventory restored:
  - Apr 20: 12 available
  - Apr 21: 10 available
  - Apr 22: 15 available
```

---

## 📞 SUPPORT

For any issues with booking flow:
1. Check inventory exists for dates
2. Verify hotel is approved
3. Verify room type is active
4. Check availability via search API first
5. Review error messages for specifics

---

**This document explains the COMPLETE booking flow with inventory management!** 🎉
