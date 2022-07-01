<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurcharseController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Test servicio de la Api
Route::get('', [Controller::class, 'testApi']);

// Autenticación y generacion de token
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    
    // Cerrar sesión
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::middleware('products')->group(function () {

        // Productos CRUD
        Route::post('/product/new', [ProductController::class, 'store']);
        
        Route::delete('/product/delete/id', [ProductController::class, 'destroy']);
        
        Route::put('/product/edit/id', [ProductController::class, 'update']);
    });

    Route::get('/product/storehouse', [ProductController::class, 'index']);
    /* Fin Productos CRUD */

    Route::middleware('admin.roles')->group(function () {

        // Usuarios CRUD
        Route::post('/user/new', [UserController::class, 'store']);
        
        Route::delete('/user/delete/id', [UserController::class, 'destroy']);
        
        Route::put('/user/edit/id', [UserController::class, 'update']);

        Route::get('/user/list/all', [UserController::class, 'index']);
        /* Fin Usuarios CRUD */

        // Roles CRUD
        Route::post('/role/new', [RoleController::class, 'store']);
        
        Route::delete('/role/delete/id', [RoleController::class, 'destroy']);
        
        Route::put('/role/edit/id', [RoleController::class, 'update']);

        Route::get('/role/list/all', [RoleController::class, 'index']);
        /* Fin Roles CRUD */
    });

    // Compras echas por clientes
    Route::post('/purchase/new', [PurcharseController::class, 'createPurchase']);

    Route::get('/purchase/view/id', [PurcharseController::class, 'show']);

    Route::get('/purchase/list/all', [PurcharseController::class, 'index']);
    /* Fin Compras CRUD */

});
