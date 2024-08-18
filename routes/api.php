<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get_all_invoices', [InvoiceController::class, 'getInvoices']);
Route::get('/search', [InvoiceController::class, 'searchBar']);
Route::get('/get_customers', [CustomerController::class, 'getCustomers']);
Route::get('/get_products', [ProductController::class, 'getProducts']);
Route::post('/invoices', [InvoiceController::class, 'store']);


Route::post('/invoices', [InvoiceController::class, 'store']);
