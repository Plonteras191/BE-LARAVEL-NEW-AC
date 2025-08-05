<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController; // Add this import

/*
|--------------------------------------------------------------------------
| API Routes for Booking System
|--------------------------------------------------------------------------
*/

// Booking routes (your existing routes)
Route::prefix('bookings')->group(function () {
    // Get available dates
    Route::get('/available-dates', [BookingController::class, 'getAvailableDates']);

    // Check date availability for specific dates
    Route::post('/check-date-availability', [BookingController::class, 'checkDateAvailability']);

    // Get bookings by date (for debugging)
    Route::get('/by-date', [BookingController::class, 'getBookingsByDate']);

    // CRUD operations
    Route::get('/', [BookingController::class, 'index']); // Get all bookings with filters
    Route::post('/', [BookingController::class, 'store']); // Create new booking
    Route::get('/{id}', [BookingController::class, 'show']); // Get specific booking
    Route::patch('/{id}/status', [BookingController::class, 'updateStatus']); // Update booking status
    Route::patch('/{id}/cancel', [BookingController::class, 'cancel']); // Cancel booking
});

// Add these new Appointment routes for admin management
Route::prefix('appointments')->group(function () {
    // Get all appointments (admin view)
    Route::get('/', [AppointmentController::class, 'index']);

    // Get all technicians
    Route::get('/technicians', [AppointmentController::class, 'getTechnicians']);

    // Accept an appointment
    Route::post('/{id}/accept', [AppointmentController::class, 'accept']);

    // Cancel (reject) an appointment
    Route::delete('/{id}', [AppointmentController::class, 'destroy']);

    // Complete an appointment
    Route::post('/{id}/complete', [AppointmentController::class, 'complete']);

    // Reschedule an appointment service
    Route::post('/{id}/reschedule', [AppointmentController::class, 'reschedule']);

    // Assign technicians to an appointment
    Route::post('/{id}/assign-technicians', [AppointmentController::class, 'assignTechnicians']);
});

// Optional: Add CORS middleware if needed
// Route::middleware(['cors'])->group(function () {
//     // Your routes here
// });
