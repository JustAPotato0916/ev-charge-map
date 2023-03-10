<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            return response()->json(auth()->user(), 200);
        } catch (\Exception $e) {
            logger('Error in UserController.update: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong getting the user details'], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request)
    {
        try {
            $user = User::FindOrFail(auth()->user()->id);

            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');

            $user->save();

            return response()->json(['message' => 'User details updated!'], 200);
        } catch (\Exception $e) {
            logger('Error in UserController.update: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong updating user' . $user->id], $e->getCode());
        }
    }
}
