<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;  // Use App\Models\User for Lumen 8+
use Illuminate\Http\Response;

class UserController extends Controller 
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUsers()
    {
        $users = User::all();
        return response()->json($users, Response::HTTP_OK);
    }

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users);
    }

    public function add(Request $request)
    {
        $rules = [
            'name'     => 'required|max:50',  // Use 'name' instead of 'username'
            'email'    => 'required|email|unique:tbluser,email', // Validate email
            'password' => 'required|max:20',
        ];

        $this->validate($request, $rules);

        $user = User::create($request->all());

        return $this->successResponse($user, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->successResponse($user);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name'     => 'max:50',  // Use 'name' instead of 'username'
            'email'    => 'email|unique:tbluser,email,' . $id, // Validate email
            'password' => 'max:20',
        ];

        $this->validate($request, $rules);

        $user = User::findOrFail($id);
        $user->fill($request->all());

        if ($user->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->save();
        return $this->successResponse($user);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->successResponse($user);
    }

    /**
     * Helper method: Return a success response
     */
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json(['data' => $data], $code);
    }

    /**
     * Helper method: Return an error response
     */
    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}