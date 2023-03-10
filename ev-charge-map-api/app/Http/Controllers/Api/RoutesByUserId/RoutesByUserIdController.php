<?php

namespace App\Http\Controllers\Api\RoutesByUserId;

use App\Http\Controllers\Controller;
use App\Models\Route;

class RoutesByUserIdController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            return response()->json([
                'routesByUserId' => Route::where('user_id', '=', auth()->user()->id)->orderBy('created_at', 'desc')->get()
            ], 200);
        } catch (\Exception $e) {
            logger('Error in RoutesByUserIdController.show: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong getting routes by user id'], $e->getCode());
        }
    }
}
