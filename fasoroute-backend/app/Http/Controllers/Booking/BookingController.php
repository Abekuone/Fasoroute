<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function store(Request $request, $tripId)
    {
        $trip = Trip::find($tripId);

        if (!$trip) {
            return response()->json([
                'status' => 404,
                'error' => 'Trajet non trouvé',
                'message' => 'Trajet introuvable',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'seats_reserved' => 'required|integer|min:1|max:' . $trip->available_seats,
            'extra_luggage' => 'integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        $booking = Booking::create([
            'passenger_id' => Auth::id(),
            'trip_id' => $trip->id,
            'seats_reserved' => $request->input('seats_reserved'),
            'extra_luggage' => $request->input('extra_luggage'),
            'total_price' => $request->input('seats_reserved') * $trip->price_per_passenger + $request->input('seats_reserved'),
        ]);

        $trip->update([
            'available_seats' => $trip->available_seats - $request->input('seats_reserved'),
        ]);

        return response()->json([
            'status' => 201,
            'success' => 'Réservation créée avec succès',
            'booking' => $booking,
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        $bookings = Booking::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 200,
            'bookings' => $bookings,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();

        $booking = Booking::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => 404,
                'error' => 'Réservation non trouvée',
                'message' => 'Réservation introuvable',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'booking' => $booking,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $booking = Booking::where('passenger_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => 404,
                'error' => 'Réservation non trouvée',
                'message' => 'Réservation introuvable',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'seats_reserved' => 'integer|min:1|max:' . $booking->trip->available_seats,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        $booking->update([
            'seats_reserved' => $request->input('seats_reserved'),
            'total_price' => $request->input('seats_reserved') * $booking->trip->price_per_passenger,
        ]);

        return response()->json([
            'status' => 200,
            'success' => 'Réservation mise à jour avec succès',
            'booking' => $booking,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $booking = Booking::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => 404,
                'error' => 'Réservation non trouvée',
                'message' => 'Réservation introuvable',
            ], 404);
        }

        $booking->delete();

        $booking->trip->update([
            'available_seats' => $booking->trip->available_seats + $booking->seats_reserved,
        ]);

        return response()->json([
            'status' => 200,
            'success' => 'Réservation annulée avec succès',
        ]);
    }
}
