<?php

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
    return view('welcome');
});

Route::post('/cargarCaja', 'TransaccionController@cargarCaja');
Route::get('/estadoCaja', 'TransaccionController@estadoCaja');
Route::get('/vaciarCaja', 'TransaccionController@vaciarCaja');
Route::post('/realizarPago', 'TransaccionController@realizarPago');
Route::get('/estadoCajaFechaHora/{fecha}', 'TransaccionController@estadoCajaFechaHora');
Route::get('/verLogs', 'TransaccionController@verLogs');
