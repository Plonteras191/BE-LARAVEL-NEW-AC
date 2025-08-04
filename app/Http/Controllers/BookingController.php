<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\BookingService;
use App\Models\BookingAcType;
use App\Models\AcType;
use App\Models\BookingStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Get available dates with proper booking limit check (2 customers per date)
    public function getAvailableDates(Request $request)
    {
        $start = $request->input('start', '2025-01-01');
        $end = $request->input('end', '2025-12-31');

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        // Generate all dates in the range
        $allDates = [];
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $allDates[] = $date->format('Y-m-d');
        }

        // Get dates with DISTINCT booking counts (unique customers per date)
        $bookedDates = DB::table('booking_services')
            ->join('bookings', 'booking_services.booking_id', '=', 'bookings.id')
            ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
            ->whereIn('booking_statuses.status_name', ['Pending', 'Accepted'])
            ->whereNull('bookings.cancelled_at') // Only active bookings
            ->whereDate('appointment_date', '>=', $startDate)
            ->whereDate('appointment_date', '<=', $endDate)
            ->select('appointment_date', DB::raw('COUNT(DISTINCT bookings.id) as booking_count'))
            ->groupBy('appointment_date')
            ->get();

        // Create a lookup array with date => count
        $dateCountMap = [];
        foreach ($bookedDates as $bookedDate) {
            $dateCountMap[$bookedDate->appointment_date] = $bookedDate->booking_count;
        }

        // Filter dates where booking count < 2 (our limit for unique customers)
        $availableDates = [];
        foreach ($allDates as $date) {
            if (!isset($dateCountMap[$date]) || $dateCountMap[$date] < 2) {
                $availableDates[] = $date;
            }
        }

        return response()->json($availableDates);
    }

    public function checkDateAvailability(Request $request)
    {
        $dates = $request->input('dates', []);

        if (empty($dates)) {
            return response()->json([
                'error' => 'No dates provided for checking'
            ], 400);
        }

        $result = [];

        foreach ($dates as $date) {
            // Get count of DISTINCT active bookings on this date (unique customers)
            $count = DB::table('booking_services')
                ->join('bookings', 'booking_services.booking_id', '=', 'bookings.id')
                ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
                ->whereIn('booking_statuses.status_name', ['Pending', 'Accepted'])
                ->whereNull('bookings.cancelled_at') // Only active bookings
                ->whereDate('appointment_date', $date)
                ->distinct('bookings.id')
                ->count('bookings.id');

            $result[$date] = [
                'available' => ($count < 2),
                'remaining_slots' => 2 - $count
            ];
        }

        return response()->json([
            'dates' => $result
        ]);
    }

    // Create a new booking with date validation
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'completeAddress' => 'required|string',
            'services' => 'required|array|min:1',
            'services.*.type' => 'required|string|max:50',
            'services.*.date' => 'required|date',
            'services.*.time' => 'nullable|date_format:H:i',
            'services.*.acTypes' => 'required|array|min:1',
            'services.*.acTypes.*.type' => 'required|string',
            'services.*.acTypes.*.quantity' => 'required|integer|min:1'
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get unique dates from services (in case multiple services on same date)
            $serviceDates = array_unique(array_map(function($service) {
                return $service['date'];
            }, $request->input('services')));

            // Check if any of the requested dates exceed the booking limit (2 customers per date)
            foreach ($serviceDates as $date) {
                $existingBookingCount = DB::table('booking_services')
                    ->join('bookings', 'booking_services.booking_id', '=', 'bookings.id')
                    ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
                    ->whereIn('booking_statuses.status_name', ['Pending', 'Accepted'])
                    ->whereNull('bookings.cancelled_at') // Only active bookings
                    ->whereDate('appointment_date', $date)
                    ->distinct('bookings.id')
                    ->count('bookings.id');

                if ($existingBookingCount >= 2) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Date $date is no longer available. Please select another date."
                    ], 400);
                }
            }

            // Find existing customer with exact name and phone match or create new one
            $existingCustomer = Customer::where('phone', $request->input('phone'))
                                      ->where('name', $request->input('name'))
                                      ->first();

            if ($existingCustomer) {
                // Update existing customer's information if found
                $existingCustomer->update([
                    'email' => $request->input('email'),
                    'complete_address' => $request->input('completeAddress')
                ]);
                $customer = $existingCustomer;
            } else {
                // Create new customer record
                $customer = Customer::create([
                    'name' => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'complete_address' => $request->input('completeAddress')
                ]);
            }

            // Get the "Pending" status (assuming it exists with id 1, or create it)
            $pendingStatus = BookingStatus::firstOrCreate([
                'status_name' => 'Pending'
            ]);

            // Create the main booking record
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'status_id' => $pendingStatus->id
            ]);

            // Process each service
            foreach ($request->input('services') as $service) {
                // Create booking service record
                $bookingService = BookingService::create([
                    'booking_id' => $booking->id,
                    'service_type' => $service['type'],
                    'appointment_date' => $service['date'],
                    'appointment_time' => $service['time'] ?? null
                ]);

                // Process AC types for this service
                foreach ($service['acTypes'] as $acTypeData) {
                    // Find or create the AC type
                    $acType = AcType::firstOrCreate([
                        'type_name' => $acTypeData['type']
                    ]);

                    // Create the booking AC type relationship with quantity
                    BookingAcType::create([
                        'booking_service_id' => $bookingService->id,
                        'ac_type_id' => $acType->id,
                        'quantity' => $acTypeData['quantity']
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'bookingId' => $booking->id,
                'customerId' => $customer->id,
                'message' => 'Booking created successfully'
            ]);

        } catch (\Exception $e) {
            // Roll back the transaction if something goes wrong
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get booking details with all relationships
    public function show($id)
    {
        try {
            $booking = Booking::with([
                'customer',
                'status',
                'bookingServices.acTypes',
                'bookingServices.bookingAcTypes.acType',
                'technicians',
                'revenue'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    // Update booking status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        try {
            $booking = Booking::findOrFail($id);

            $status = BookingStatus::where('status_name', $request->input('status'))->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            $booking->update([
                'status_id' => $status->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel booking
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'cancelled_by' => 'nullable|integer|exists:customers,id'
        ]);

        try {
            $booking = Booking::findOrFail($id);

            $booking->update([
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('cancellation_reason'),
                'cancelled_by' => $request->input('cancelled_by')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get all bookings with filters
    public function index(Request $request)
    {
        $query = Booking::with([
            'customer',
            'status',
            'bookingServices.acTypes',
            'bookingServices.bookingAcTypes.acType'
        ]);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->whereHas('status', function($q) use ($request) {
                $q->where('status_name', $request->input('status'));
            });
        }

        // Filter by active/cancelled bookings
        if ($request->has('include_cancelled') && !$request->boolean('include_cancelled')) {
            $query->active(); // Use the scope from the model
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('bookingServices', function($q) use ($request) {
                $q->whereBetween('appointment_date', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            });
        }

        // Filter by customer if provided
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    // Helper method to get bookings by date for debugging
    public function getBookingsByDate(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json([
                'error' => 'Date parameter is required'
            ], 400);
        }

        $bookings = DB::table('booking_services')
            ->join('bookings', 'booking_services.booking_id', '=', 'bookings.id')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
            ->leftJoin('booking_actypes', 'booking_services.id', '=', 'booking_actypes.booking_service_id')
            ->leftJoin('ac_types', 'booking_actypes.ac_type_id', '=', 'ac_types.id')
            ->whereDate('appointment_date', $date)
            ->whereIn('booking_statuses.status_name', ['Pending', 'Accepted'])
            ->whereNull('bookings.cancelled_at') // Only active bookings
            ->select(
                'customers.name',
                'customers.phone',
                'booking_services.service_type',
                'booking_services.appointment_date',
                'booking_services.appointment_time',
                'booking_statuses.status_name',
                'bookings.id as booking_id',
                'ac_types.type_name as ac_type',
                'booking_actypes.quantity'
            )
            ->get();

        $uniqueBookings = $bookings->groupBy('booking_id')->count();

        return response()->json([
            'date' => $date,
            'total_bookings' => $uniqueBookings,
            'total_services' => $bookings->count(),
            'available_slots' => 2 - $uniqueBookings,
            'bookings' => $bookings
        ]);
    }
}
