<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller {
    public function __construct() {
        $this->middleware(
            'auth:api', ['except' => ['login', 'register']]
        );
    }

    /**
     * Logear usuario
     * @param request credenciales del usuario
     * @return object token y datos del usuario
     */
    public function login( Request $request ) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4'
        ]);

        if ( $validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = ( 24 * 60 );
        $this->guard()->factory()->setTTL( $token_validity );

        $token = $this->guard()->attempt( $validator->validated() );

        if( !$token ) {
            return response()->json(['error' => 'Unauthorized!'], 401);
        }

        return response()->json([
            'user' => $this->guard()->user(),
            'accessToken' => $token
        ], 200);
    }

    public function register( Request $request ) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'terms_agree' => 'required'
        ]);

        if ( $validator->fails() ) {
            return response()->json([
                $validator->errors()
            ], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User created successfully',
            'urser' => $user
        ]);
    }

    public function whoami() {
        return response()->json([
            'user' => $this->guard()->user()
        ], 200);
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
        return $this->respondWithToken($this->guard()->refresh());
    }

    /* protected function respondWithToken( $token ) {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
        ]);
    } */

    protected function guard() {
        return Auth::guard();
    }

    public function edit(Request $request) {
        $user = $this->guard()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id,'id'),
            ],
            'password' => 'nullable|min:6|confirmed',

        ], [
            'required' => 'Este campo es necesario.',
            'email' => 'El correo es inválido.',
            'unique' => 'Este correo eletrónico ya está en uso.',
            'confirmed' => 'Las contraseñas no coinciden.',

        ]);

        if ( $validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();

        $user->name = $data['name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];

        if(null != $data['password']) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();
        return response()->json([
            'success' => true
        ]);
    }
}
