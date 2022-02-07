<?php

namespace App\Http\Controllers;

use App\Models\{User, PersonalInformation};
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    /**
     * Registra un usuario
     *
     * @param Request $request: Petición http por metodo post
     *
     * @return array con mensaje de error o éxito
     */
    public function register(Request $request) {

        # Validación de los campos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'terms_agree' => 'required'
        ]);

        # Validamos los campos ye ncaso de error devolvemos los errores en un array
        if ( $validator->fails() ) {
            return response()->json([
                'message' => $validator->errors(),
                'success' => false,
            ], 500);
        }

        # Datos a insertar
        $data = $validator->validated();

        # Encriptar contraseña
        $data['password'] = Hash::make($data['password']);

        # Creamos al usuario
        $user = User::create($data);

        # Creamos registro de información personal
        PersonalInformation::create([
            'user_id' => $user->id
        ]);

        # Mensaje de éxito
        return response()->json([
            'success' => true,
            'message' => 'Registrado exitosamente.'
        ]);
    }

    /**
     * Inicia la sesión de un usuario
     *
     * @param Request $request: Petición http por metodo post
     *
     * @return array con mensaje de error o éxito
     */
    public function login(Request $request){
        # Validar campos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4'
        ]);

        # Si hay errores devolvemos un array con los errores
        if ( $validator->fails() ) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        # Verificamos credenciales con a bases de datos
        $token = Auth::attempt($validator->validated());

        # En caso de no encontrar al usuario
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        # Mensaje de éxito
        return response()->json([
            'success' => true,
            'message' => 'Has iniciado sesión de forma exitosa.',
            'token' => $this->respondWithToken($token),
            'user' => auth()->user()->load('personal')
        ]);

    }

    /**
     * Edita los datos personales de un usuario
     *
     * @param Request $request: Petición http por metodo post
     *
     * @return array con mensaje de error o éxito
     */
    public function edit(Request $request) {
        # Usuario conectado
        $user = auth()->user();

        # Validamos los campos
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

        # En caso de haber errores
        if ( $validator->fails() ) {
            return response()->json($validator->errors(), 400);
        }


        # Obtenemos lo datos y los asignamos al usuario
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

        # Guardamos los datos
        $user->personal->save();

        # Resuesta de éxito
        return response()->json([
            'success' => true,
            'message' => 'Guardado exitosamente.'
        ]);
    }

    /**
     * Obtiene al usuario actualmente conectado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function owner()
    {
        return response()->json(auth()->user()->load('personal'));
    }

    /**
     * Cierra la sesión de un usuario, invalida el token actual del usuario conectado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Has cerrado sesión con éxito.'
        ]);
    }

    /**
     * Genera un nuevo token de usuario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    /**
     * Obtiene le toen con la estructura adecuada
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

}
