# Real-Time Booking System dengan WebSocket & Broadcasting

Sistem booking ini mengimplementasikan real-time updates menggunakan Laravel Broadcasting dan WebSocket untuk menangani Race Condition dan memberikan pengalaman pengguna yang lebih baik.

## Fitur Utama

### 1. **Race Condition Handling**
- Menggunakan database transactions dengan `lockForUpdate()` untuk prevent concurrent booking pada kursi yang sama
- Setiap booking request di-lock secara atomik untuk memastikan data consistency
- Query dengan pessimistic lock memastikan hanya satu transaction yang bisa modify kursi pada waktu yang sama

### 2. **Real-Time Seat Updates**
- Ketika kursi di-book, event `SeatBooked` di-broadcast ke semua client yang menonton schedule tersebut
- Ketika kursi di-free up (booking dibatalkan), event `SeatAvailable` di-broadcast
- Frontend instantly mengupdate UI tanpa perlu refresh halaman

### 3. **WebSocket Implementation**
- Menggunakan Pusher atau Laravel Reverb untuk real-time communication
- Channel-based subscription untuk schedule-specific updates
- Private channels untuk user-specific notifications

## Setup Instructions

### Option 1: Menggunakan Pusher (Cloud-based)

1. **Install Pusher Package**
```bash
composer require pusher/pusher-php-server
```

2. **Setup Environment Variables**
Tambahkan ke `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_APP_ENCRYPTED=true
```

3. **Verifikasi Konfigurasi**
```
php artisan tinker
broadcast('test', ['message' => 'Hello'])->toOthers();
```

### Option 2: Menggunakan Laravel Reverb (Self-hosted)

1. **Install Reverb**
```bash
composer require laravel/reverb
php artisan reverb:install
```

2. **Setup Environment Variables**
```
BROADCAST_DRIVER=reverb
REVERB_APP_ID=cinetix
REVERB_APP_KEY=cinetix-key
REVERB_APP_SECRET=cinetix-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

3. **Start Reverb Server**
```bash
php artisan reverb:start --debug
```

4. **Update broadcasting.php**
```php
'reverb' => [
    'driver' => 'reverb',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'host' => env('REVERB_HOST', '127.0.0.1'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
    ],
],
```

## How It Works

### 1. **Booking Process**
```
User selects seats → POST /booking/store
  ↓
Database transaction with pessimistic lock
  ↓
Check seat availability (with lock)
  ↓
Create booking & update seat status
  ↓
Broadcast SeatBooked event
  ↓
All clients receive real-time update
  ↓
UI updates seat color to red (booked)
```

### 2. **Event Broadcasting**

#### SeatBooked Event
```php
broadcast(new SeatBooked($seat, $schedule))->toOthers();
```
**Channel:** `booking.schedule.{schedule_id}`
**Data:** seat_id, seat_code, status, schedule_id

#### SeatAvailable Event
```php
broadcast(new SeatAvailable($seat))->toOthers();
```
**Channel:** `booking.studio.{studio_id}`
**Data:** seat_id, seat_code, status

#### BookingConfirmed Event
```php
broadcast(new BookingConfirmed($booking))->toOthers();
```
**Channel:** `user.{user_id}` (Private)
**Data:** booking_id, status, total_amount, qr_redeem

### 3. **Frontend Real-Time Updates**
```javascript
// Subscribe to schedule channel
const channel = pusher.subscribe(`booking.schedule.${scheduleId}`);

// Listen for seat booked event
channel.bind('seat-booked', function(data) {
    updateSeatStatus(data.seat_id, 'booked');
    // Change seat button color to red
});

// Listen for seat available event
channel.bind('seat-available', function(data) {
    updateSeatStatus(data.seat_id, 'available');
    // Change seat button color to green
});
```

## Database Transactions & Locking

### Pessimistic Locking
```php
DB::transaction(function () {
    $schedule = Schedule::lockForUpdate()->find($id);
    $seats = Seat::whereIn('id', $seatIds)->lockForUpdate()->get();
    
    // Check & book seats atomically
    $booking = Booking::create($data);
});
```

**Keuntungan:**
- Prevents lost updates
- Ensures data consistency
- Serializable transactions

### Retry Logic
```php
DB::transaction(function () {
    // code
}, 3); // Retry up to 3 times
```

## Monitoring & Debugging

### Laravel Logs
```
storage/logs/laravel.log
```

### Pusher Debugger
- Console di dashboard Pusher
- Real-time event monitoring

### Reverb Logs
```
tail -f storage/logs/reverb.log
```

## Security Considerations

1. **Authentication Checks**
   - Verify user ownership of booking
   - Use Policies for authorization

2. **Rate Limiting**
   - Limit booking requests per user
   - Prevent bot attacks

3. **CSRF Protection**
   - All POST requests require CSRF token
   - Built-in Laravel protection

4. **Broadcast Authorization**
   ```php
   // routes/channels.php
   Broadcast::channel('booking.schedule.{id}', function ($user) {
       return true; // Public channel
   });

   Broadcast::private('user.{id}', function ($user, $id) {
       return $user->id === $id; // Private channel
   });
   ```

## Performance Optimization

1. **Database Indexing**
```sql
ALTER TABLE ticket_bookings ADD INDEX idx_booking_status (booking_id, status);
ALTER TABLE seats ADD INDEX idx_studio_status (studio_id, status);
ALTER TABLE schedules ADD INDEX idx_schedule_date (schedule_date);
```

2. **Query Optimization**
   - Use `with()` untuk eager loading
   - Minimize N+1 queries
   - Use `pluck()` untuk single column queries

3. **Caching**
```php
$availableSeats = Cache::remember(
    "schedule.{$schedule->id}.seats",
    3600,
    fn() => $schedule->studio->seats()->where('status', 'available')->get()
);
```

## Testing

### Unit Tests
```bash
php artisan test tests/Unit/BookingTest.php
```

### Feature Tests
```bash
php artisan test tests/Feature/BookingFeatureTest.php
```

### Load Testing (untuk real-time updates)
```bash
composer require --dev laravel-echo-server
npm install -D pusher-js echo
```

## Troubleshooting

### WebSocket Connection Failed
1. Check CORS settings di `config/broadcasting.php`
2. Verify Pusher/Reverb credentials
3. Check firewall rules

### Events Not Broadcasting
1. Verify `ShouldBroadcast` interface implemented
2. Check `broadcastOn()` returns valid channel
3. Run `php artisan config:cache` for caching issues

### Race Condition Still Happening
1. Verify `lockForUpdate()` digunakan pada both schedule & seats
2. Check transaction isolation level
3. Ensure proper error handling dalam catch block

## Future Enhancements

1. **Queue Jobs** untuk heavy operations
2. **WebSocket Fallback** ke polling jika WebSocket gagal
3. **Seat Expiration** - Free seats jika user tidak complete booking dalam waktu X menit
4. **Analytics** - Track popular seats, peak booking times
5. **Mobile Push Notifications** untuk booking updates
6. **Payment Webhook** integration untuk auto-confirmation

---

**Last Updated:** May 13, 2026
**Status:** Production Ready
