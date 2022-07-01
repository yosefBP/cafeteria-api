<?php

namespace App\Http\Controllers\Api;

use App\Events\StockAlertNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purcharse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurcharseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $compras = Purcharse::where('user_id', Auth::user()->id)->get();
            if (count($compras) > 0)
                return $compras->toArray();
            return response()->json(['Error' => 'purchases not found'], 400);
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
    public function createPurchase(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'compra' => 'required|array',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
            // Insercion de compra cliente en DB
            if (count($request->compra) === 1) {
                $id_sale = $this->registerPurchase($request->compra);
                $this->purchaseDetail($id_sale, $request->compra[0]);
            } elseif (count($request->compra) > 1) {
                $id_sale = $this->registerPurchase($request->compra);
                foreach($request->compra as $detalle)
                    $this->purchaseDetail($id_sale, $detalle);
            }

            return response('purchase created', 201);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sale_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }
            $detalleCompra = Purcharse::select(['products.nombre_producto',
                                         'product_sale.cantidad_producto', 'product_sale.valor',
                                          'sales.created_at as fecha_compra'])
                                    ->where('sales.id', $request->sale_id)
                                    ->leftJoin('product_sale', 'product_sale.sale_id', '=', 'sales.id')
                                    ->leftJoin('products', 'products.id', '=', 'product_sale.product_id')
                                    ->get();
        
            return $detalleCompra;
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);

        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {   
        //
    }

    /**
     * Registra el total de compra realizada por el cliente
     *
     * @param  array  $compra
     * 
     * @return int $newPurchase
     */
    static function registerPurchase($compra)
    {
        try{
            if (count($compra) == 1) {
                $precioUnidad = Product::select('precio')
                                        ->where('id', '=', $compra[0]['product_id'])
                                        ->first();

                $newPurchase = new Purcharse();
                $newPurchase->user_id = Auth::user()->id;
                $newPurchase->total_compra = $precioUnidad->precio * $compra[0]['cantidad_producto'];
                $newPurchase->save();
                return $newPurchase->id;
            } else {
                $total_compra = 0;
                foreach ($compra as $detalle){
                    $precioUnidad = Product::select('precio')
                                            ->where('id', '=', $detalle['product_id'])
                                            ->first();

                    $total_compra = $total_compra + ($precioUnidad->precio * $detalle['cantidad_producto']);
                }

                $newPurchase = new Purcharse();
                $newPurchase->user_id = Auth::user()->id;
                $newPurchase->total_compra = $total_compra;
                $newPurchase->save();
                return $newPurchase->id;
            }
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registra el id de la compra, producto y valor segun la cantidad 
     *
     * @param  int  $id_sale
     * @param array $detalle
     */
    static function purchaseDetail($id_sale, $detalle)
    {
        try{
            $valorUnidad = Product::select('precio')->where('id', $detalle['product_id'])->first();
            /* Inicio de transacciones en BD */
            DB::beginTransaction();
            DB::table('product_sale')->insert([
                'sale_id' => $id_sale,
                'product_id' => $detalle['product_id'],
                'cantidad_producto' => $detalle['cantidad_producto'],
                'valor' => $detalle['cantidad_producto'] * $valorUnidad->precio,
            ]);
            // Actualizar stock de producto
            $dataProduct = Product::find($detalle['product_id']);
            $dataProduct->stock = $dataProduct->stock - $detalle['cantidad_producto'];
            $dataProduct->save();
            DB::commit();
            /* Fin de transacciones en BD */
            if ($dataProduct->stock <= 9)
                event(new StockAlertNotification($dataProduct));

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }
}
