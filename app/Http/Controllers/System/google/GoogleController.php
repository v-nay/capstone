<?php

namespace App\Http\Controllers\System\google;

use App\Http\Controllers\System\ResourceController;
use App\Services\System\GoogleService;
use Illuminate\Http\Request;

class GoogleController extends ResourceController
{
    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function moduleName()
    {
        return 'Google';
    }

    public function viewFolder()
    {
        return 'system.google';
    }

    public function searchMotels(Request $request)
    {
        $city = $request->get('city', 'New York');
        $location = $request->get('location', '40.712776,-74.005974'); // Example for New York
        $radius = $request->get('radius', 5000); // Default to 5 km

        $results = $this->googleService->searchPlaces(
            "motels in $city",
            // $location,
            $radius
        );

        return response()->json($results);
    }

    public function searchAllMotels(Request $request)
    {
        $suburb = $request->get('suburb', 'Hornsby');
        $radius = $request->get('radius', 5000);

        try {
            // Get the location (latitude, longitude) for the suburb
            $location = $this->googleService->getSuburbLocation($suburb);

            // Fetch all motels using pagination
            $results = $this->googleService->searchPlacesWithPagination(
                'motels',
                $location,
                $radius
            );

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
