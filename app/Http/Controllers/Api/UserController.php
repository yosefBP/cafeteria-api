<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::select(['users.id', 'users.nombre', 'users.email', 'users.role_id', 'roles.rol'])
                            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                            ->get();

            if (count($users->toArray()) > 0)
                return $users->toArray();
            return response()->json(['Error' => 'users not found'], 400);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'role_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }

            $newUser = new User();
        
            $newUser->nombre = $request->nombre;
            $newUser->email = $request->email;
            $newUser->password = $request->password;
            $newUser->role_id = $request->role_id;
            $newUser->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' ha creado un nuevo usuario con id: ' . $newUser->id);
            return response('user created', 201);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'nombre' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'role_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
            $dataUser = User::find($request->id);
            if ($dataUser == null)
                return response()->json(['Error' => 'user '.$request->id.' not found'], 400);

            $dataUser->nombre = $request->nombre;
            $dataUser->email = $request->email;
            $dataUser->password = $request->password;
            $dataUser->role_id = $request->role_id;
            $dataUser->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' ha actualizado el usuario con id: ' . $dataUser->id);
            return response()->json(['status' => 'user '.$request->id.' updated'], 204);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(['Error' => $validator->errors()], 400);
        }

        $product = User::find($request->id);
        if ($product == null)
            return response()->json(['Error' => 'id user '.$request->id.' not found'], 400);
        $product->delete();

        Log::notice('El usuario con id: ' . Auth::id() . ' ha eliminado el usuario con id: ' . $product->id);
        return response()->json(['status' => 'user '.$request->id.'  deleted'], 204);
    }
}
