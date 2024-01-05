<?php

namespace App\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Trip::query();

        if ($request->has('destination')) {
            $query->where('destination', $request->input('destination'));
        }

        if ($request->has('departure_time')) {
            $query->where('departure_time', '>=', $request->input('departure_time'));
        }

        if ($request->has('departure_location')) {
            $query->where('departure_location', $request->input('departure_location'));
        }

        if ($request->has('price_per_passenger')) {
            $query->where('price_per_passenger', $request->input('price_per_passenger'));
        }

        $trips = $query->get();

        return response()->json([
            'status' => 200,
            'success' => 'Trajets disponibles',
            'trips' => $trips,
        ]);
    }

    public function show($id)
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json([
                'status' => 404,
                'error' => 'Trajet non trouvé',
                'message' => 'Le trajet non trouvé',
            ]);
        }

        return response()->json([
            'status' => 200,
            'success' => 'Trajet trouvé',
            'trip' => $trip,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departure_location' => 'required|string',
            'departure_location_precise' => 'required|string',
            'destination' => 'required|string',
            'destination_precise' => 'required|string',
            'route' => 'required|string',
            'phone_number' => 'required|string',
            'departure_time' => 'required|date',
            'available_seats' => 'required|integer|min:1',
            'price_per_passenger' => 'required|numeric|min:0',
            'is_return_trip' => 'boolean',
            'return_departure_location' => 'string|nullable',
            'return_departure_location_precise' => 'string|nullable',
            'return_destination' => 'string|nullable',
            'return_destination_precise' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        $trip = Trip::create([
            'driver_id' => Auth::id(),
            'status' => 'available',
            'departure_location' => $request->input('departure_location'),
            'departure_location_precise' => $request->input('departure_location_precise'),
            'destination' => $request->input('destination'),
            'destination_precise' => $request->input('destination_precise'),
            'route' => $request->input('route'),
            'phone_number' => $request->input('phone_number'),
            'departure_time' => $request->input('departure_time'),
            'available_seats' => $request->input('available_seats'),
            'price_per_passenger' => $request->input('price_per_passenger'),
            'is_return_trip' => $request->input('is_return_trip', false),
            'return_departure_location' => $request->input('return_departure_location'),
            'return_departure_location_precise' => $request->input('return_departure_location_precise'),
            'return_destination' => $request->input('return_destination'),
            'return_destination_precise' => $request->input('return_destination_precise'),
        ]);

        return response()->json([
            'status' => 201,
            'success' => 'Trajet publié avec succès',
            'trip' => $trip,
        ]);
    }

    public function update(Request $request, $id)
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json([
                'status' => 404,
                'error' => 'Trajet non trouvé',
                'message' => 'Trajet introuvable',
            ]);
        }

        if (Auth::id() !== $trip->driver_id) {
            return response()->json([
                'status' => 403,
                'error' => 'Accès non autorisé',
                'message' => 'Vous n\'êtes pas autorisé à mettre à jour ce trajet',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'departure_location' => 'required|string',
            'departure_location_precise' => 'required|string',
            'destination' => 'required|string',
            'destination_precise' => 'required|string',
            'route' => 'required|string',
            'phone_number' => 'required|string',
            'departure_time' => 'required|date',
            'available_seats' => 'required|integer|min:1',
            'price_per_passenger' => 'required|numeric|min:0',
            'is_return_trip' => 'boolean',
            'return_departure_location' => 'string|nullable',
            'return_departure_location_precise' => 'string|nullable',
            'return_destination' => 'string|nullable',
            'return_destination_precise' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }

        $trip->update([
            'status' => 'available',
            'departure_location' => $request->input('departure_location'),
            'departure_location_precise' => $request->input('departure_location_precise'),
            'destination' => $request->input('destination'),
            'destination_precise' => $request->input('destination_precise'),
            'route' => $request->input('route'),
            'phone_number' => $request->input('phone_number'),
            'departure_time' => $request->input('departure_time'),
            'available_seats' => $request->input('available_seats'),
            'price_per_passenger' => $request->input('price_per_passenger'),
            'is_return_trip' => $request->input('is_return_trip', false),
            'return_departure_location' => $request->input('return_departure_location'),
            'return_departure_location_precise' => $request->input('return_departure_location_precise'),
            'return_destination' => $request->input('return_destination'),
            'return_destination_precise' => $request->input('return_destination_precise'),
        ]);

        return response()->json([
            'status' => 200,
            'success' => 'Trajet mis à jour avec succès',
            'trip' => $trip,
        ]);
    }

    public function destroy($id)
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json([
                'status' => 404,
                'error' => 'Trajet non trouvé',
                'message' => 'Le trajet introuvable',
            ]);
        }
        
        if (Auth::id() !== $trip->driver_id) {
            return response()->json([
                'status' => 403,
                'error' => 'Accès non autorisé',
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce trajet',
            ]);
        }

        $trip->delete();

        return response()->json([
            'status' => 200,
            'success' => 'Trajet supprimé avec succès',
        ]);
    }
}
