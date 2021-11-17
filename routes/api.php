<?php

use App\Http\Controllers\TransactionController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/transaction',[TransactionController::class,'create'])->name('transaction');

Route::get('/transaction',[TransactionController::class, 'index'])->name('all-transactions');
Route::get('/transaction/{transaction}',[TransactionController::class, 'show'])->name('get-transactions');
Route::delete('/transaction/{transaction}',[TransactionController::class, 'delete'])->name('delete-transaction');
