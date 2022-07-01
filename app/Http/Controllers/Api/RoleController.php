<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        if (count($roles) > 0)
            return $roles->toArray();
        return response()->json(['Error' => 'roles not found'], 400);
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
                'rol' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }

            $newRole = new Role();
        
            $newRole->rol = $request->rol;
            $newRole->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' ha creado un nuevo Rol con id: ' . $request->rol);
            return response('role created', 201);
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
                'rol' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
            $dataRole = Role::find($request->id);
            if ($dataRole == null)
                return response()->json(['Error' => 'role '.$request->id.' not found'], 400);

            $dataRole->rol = $request->rol;
            $dataRole->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' ha actualizado el Rol con id: ' . $request->id);
            return response()->json(['status' => 'role id '.$request->id.' updated'], 204);
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

        $role = Role::find($request->id);
        if ($role == null)
            return response()->json(['Error' => 'id role '.$request->id.' not found'], 400);
        $role->delete();

        Log::notice('El usuario con id: ' . Auth::id() . ' ha eliminado el Rol con id: ' . $request->id);
        return response()->json(['status' => 'role '.$request->id.' deleted'], 204);
    }
}
