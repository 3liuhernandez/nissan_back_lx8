<?php

namespace App\Http\Controllers;

use App\Models\{User, PersonalInformation};
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

        PersonalInformation::create([
            'user_id' => $user->id
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso',
            'user' => $user
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
            'document' => [
                'required',
                Rule::unique('personal_information')->ignore($user->id,'user_id'),
            ],
            'birthdate' => 'required|date_format:Y-m-d',
            'tel' => 'required',
            'adress' => 'required',
            'adress' => 'required',
            'food' => 'required|in:0,1,celiaquia,vegetarianismo,veganismo',
            'size' => 'required|in:xs,s,m,xl,xxl',
            'transport' => 'required|in:aereo,bus,no-aplica',


            'aerial_aeroline' => 'required_if:transport,aereo',
            'aerial_arrive_time' => 'required_if:transport,aereo',
            'aerial_booking' => 'required_if:transport,aereo',
            'aerial_departure_time' => 'required_if:transport,aereo',
            'aerial_destination' => 'required_if:transport,aereo',
            'aerial_flight' => 'required_if:transport,aereo',

            'bus_arrive_time' => 'required_if:transport,bus',
            'bus_booking' => 'required_if:transport,bus',
            'bus_departure_time' => 'required_if:transport,bus',

            'parking_car_model' => 'required_if:transport,no-aplica',
            'parking_patent' => 'required_if:transport,no-aplica'
        ]);

        if ( $validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }


        $data = $validator->validated();
        $user->personal->document = $data['document'];
        $user->personal->birthdate = $data['birthdate'];
        $user->personal->tel = $data['tel'];
        $user->personal->adress = $data['adress'];
        $user->personal->adress = $data['adress'];
        $user->personal->food = $data['food'];
        $user->personal->size = $data['size'];
        $user->personal->transport = $data['transport'];

        $user->personal->aerial_aeroline = $data['aerial_aeroline'];
        $user->personal->aerial_arrive_time = $data['aerial_arrive_time'];
        $user->personal->aerial_booking = $data['aerial_booking'];
        $user->personal->aerial_departure_time = $data['aerial_departure_time'];
        $user->personal->aerial_destination = $data['aerial_destination'];
        $user->personal->aerial_flight = $data['aerial_flight'];

        $user->personal->bus_arrive_time = $data['bus_arrive_time'];
        $user->personal->bus_booking = $data['bus_booking'];
        $user->personal->bus_departure_time = $data['bus_departure_time'];

        $user->personal->parking_car_model = $data['parking_car_model'];
        $user->personal->parking_patent = $data['parking_patent'];

        $user->personal->save();



        return response()->json([
            'success' => true,
            'message' => 'Guardado exitosamente.'
        ]);
    }
}
