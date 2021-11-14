<?php

use App\Http\Controllers\TransactionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $transaction = \App\Models\Transaction::find('04b84d33-0e7e-4f1e-8026-eac6b4a7305f');
    $response = new \App\Http\Resources\TransactionResource($transaction);
    return json_encode($response);
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
