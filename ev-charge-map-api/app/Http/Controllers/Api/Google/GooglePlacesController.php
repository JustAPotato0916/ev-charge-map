<?php

namespace App\Http\Controllers\Api\Google;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SKAgarwal\GoogleApi\PlacesApi;

class GooglePlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $googlePlaces = new PlacesApi(env('GOOGLE_MAPS_API_KEY'));
            $response = $googlePlaces->placeAutocomplete($request->get('input'), [
                'components' => 'country:tw',
            ]);

            return response()->json(['places' => $response], 200);
        } catch (\Exception $e) {
            logger('Something went wrong with GooglePlacesController.index', [$e->getMessage()]);
            return response()->json(['error' => 'Something went wrong getting Google Places API'], $e->getCode());
        }
    }
}
