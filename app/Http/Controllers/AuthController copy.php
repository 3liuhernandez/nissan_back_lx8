<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller {


    public function __construct() {
        $this->middleware(
            'auth:api', ['except' => ['login']]
        );
    }



    /* public function login( Request $request ) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|string|min:4'
        ]);

        if ( !$validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = 24 * 60;
        $this->guard()->factory()->setTTL( $token_validity );

        if( !$token = $this->guard()->attemp( $validator->validated() ) ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->responseWithToken( $token );
    } */

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED );
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    /* public function register( Request $request ) {
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
    } */

    /* public function profile() {
        return response()->json( $this->guard()->user() );
    } */

    /* public function logout() {
        $this->guard()->logout();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    } */

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /* protected function responseWithToken( $token ) {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
        ]);
    } */

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /* protected function guard() {
        return Auth::guard();
    } */
}
