<?php

namespace App\Http\Controllers\Api\Route;

use App\Http\Controllers\Controller;
use App\Http\Requests\Route\RouteStoreRequest;
use App\Models\Route;

class RouteController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Route\RouteStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RouteStoreRequest $request)
    {
        try {
            $route = new Route;

            $route->user_id = auth()->user()->id;
            $route->name = $request->get('name');
            $route->from = $request->get('from');
            $route->to = $request->get('to');
            $route->range = $request->get('range');

            $route->save();

            return response()->json([
                'message' => 'The route has been added successfully!',
                'route' => $route
            ], 200);
        } catch (\Exception $e) {
            logger('Error in RouteController@store: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong saving the route!'], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $route = Route::find($id);

            if (! $route) {
                return response()->json(['error' => 'The route does not exist!'], 400);
            }

            $route->delete();

            return response()->json(['success' => 'Route deleted successfully!'], 200);
        } catch (\Exception $e) {
            logger('Error in RouteController@destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong deleting the saved route!'], $e->getCode());
        }
    }
}
