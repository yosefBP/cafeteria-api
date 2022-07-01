<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::all();
            if (count($products->toArray()) > 0)
                return $products->toArray();
            return response()->json(['Error' => 'products not found'], 400);
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
                'nombre_producto' => 'required|string',
                'referencia' => 'required|string',
                'precio' => 'required|integer',
                'categoria' => 'required|string',
                'stock' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
        
            $newProduct = new Product();
        
            $newProduct->nombre_producto = $request->nombre_producto;
            $newProduct->referencia = $request->referencia;
            $newProduct->precio = $request->precio;
            $newProduct->categoria = $request->categoria;
            $newProduct->stock = $request->stock;
            $newProduct->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' creo el producto con id: ' . $request->id);
            return response('product created', 201);
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
                'nombre_producto' => 'required|string',
                'referencia' => 'required|string',
                'precio' => 'required|integer',
                'categoria' => 'required|string',
                'stock' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
            $dataProduct = Product::find($request->id);
            if ($dataProduct == null)
                return response()->json(['Error' => 'product not found'], 400);

            $dataProduct->nombre_producto = $request->nombre_producto;
            $dataProduct->referencia = $request->referencia;
            $dataProduct->precio = $request->precio;
            $dataProduct->categoria = $request->categoria;
            $dataProduct->stock = $request->stock;
            $dataProduct->save();

            Log::notice('El usuario con id: ' . Auth::id() . ' actualizo el producto con id: ' . $request->id);
            return response()->json(['status' => 'product updated'], 204);
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
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }

            Log::notice('El usuario con id: ' . Auth::id() . ' ha eliminado el producto con id: ' . $request->id);
            $product = Product::find($request->id);
            if ($product == null)
                return response()->json(['Error' => 'id product not found'], 400);
            $product->delete();

            return response()->json(['status' => 'product deleted'], 204);
        }catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }
}
