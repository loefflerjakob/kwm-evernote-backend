<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $users = DB::table('users')->get();
    return view('welcome', compact('users'));
    //return view('welcome');
});

Route::get('/users/{id}', function ($id) {
    $user = DB::table('users')->find($id);

    dd($user);
}

);
