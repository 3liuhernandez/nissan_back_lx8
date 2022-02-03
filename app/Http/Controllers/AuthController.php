<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function __construct() {
        $this->middleware(
            'auth:api', ['except' => ['login']]
        );
    }

    public function login( Request $request ) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|string|min:4'
        ]);

        if ( !$validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = 24 * 60;
        $this->guard()->factory()->setTTL( $token_validity );

        if( !$token = $this->guard()->attempt( $validator->validated() ) ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->responseWithToken( $token );
    }

    public function register( Request $request ) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'pass' => 'required|confirmed|min:4'
        ]);

        if ( !$validator->fails() ) {
            return response()->json([
                $validator->errors()
            ], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['pass' => bcrypt($request->pass)]
        ));

        return response()->json([
            'message' => 'User created successfully',
            'urser' => $user
        ]);
    }

    public function profile() {
        return response()->json( $this->guard()->user() );
    }

    public function logout() {
        $this->guard()->logout();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }

    public function refresh() {

    }

    protected function responseWithToken( $token ) {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
        ]);
    }

    protected function guard() {
        return Auth::guard();
    }
}
