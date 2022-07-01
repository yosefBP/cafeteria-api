<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Realiza la validacion de usuario y password para retornar un token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try{
            // Validacion de datos recibidos
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }

            // Validacion email del usuario en DB
            $userSearch = User::where('email', $request->email)->first();
            if ($userSearch === null)
                return response()->json(['Error' => 'user not found'], 400);

            // Validacion del Password
            if (! Hash::check($request->password, $userSearch->password)) {
                return response()->json(['Error' => 'wrong password'], 400);
            }
            // Retorna un token si supera las validaciones
            $this->destroyToken($userSearch);
            $token = $userSearch->createToken($userSearch->nombre);
            $rol = $userSearch->role;
            Log::info('Login-usuario-id: ' . $userSearch->id. ' Rol: ' . $rol->rol);
            return response()->json(['token' => $token->plainTextToken], 200);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Valida durante el login si el usuario ya inicio sesion antes.
     * Si es asi destruye el token
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroyToken($user)
    {
        try{
            $userToken = DB::table('personal_access_tokens')
                            ->where('tokenable_id', $user->id)
                            ->first();
        if ($userToken){
            $user->tokens()->where('id', $userToken->id)->delete();
            $rol = $user->role;
            Log::info('logout-Token-usuario-id: ' . $user->id. ' Rol: ' . $rol->rol);
        } 
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Valida si el usuario tiene un token activo.
     * Si es asi destruye el token
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        try{
            $this->destroyToken(Auth::user());
            return response()->json(['success' => 'Session logout'], 204);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }
}
