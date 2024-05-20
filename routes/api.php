<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KwmlistController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api','auth.jwt']], function(){
    //KWMlist Routes
    Route::post('kwmlists', [KwmlistController::class,'save']);
    Route::put('kwmlists/{id}', [KwmlistController::class, 'update']);
    Route::delete('kwmlists/{id}', [KwmlistController::class, 'delete']);
    Route::get('kwmlists/{kwmlist_id}/notes', [KwmlistController::class, 'getNotesByKwmlist']);

    //Note Routes
    Route::post('notes', [NoteController::class,'save']);
    Route::delete('notes/{id}', [NoteController::class, 'delete']);
    Route::put('notes/{id}', [NoteController::class, 'update']);
    Route::get('notes/{note_id}/todos', [NoteController::class, 'getTodosByNote']);

    //Todo Routes
    Route::post('todos', [TodoController::class,'save']);
    Route::delete('todos/{id}', [TodoController::class, 'delete']);
    Route::put('todos/{id}', [TodoController::class, 'update']);

    //Tag Routes
    Route::post('tags', [TagController::class,'save']);
    Route::delete('tags/{id}', [TagController::class, 'delete']);
    Route::put('tags/{id}', [TagController::class, 'update']);

    Route::post('auth/logout', [AuthController::class,'logout']);


});

//Auth
Route::post('auth/login', [AuthController::class,'login']);


//Kwmlist Routes
Route::get('kwmlists', [KwmlistController::class, 'index']);
Route::get('kwmlists/{id}', [KwmlistController::class,'findById']);


//Note Routes
Route::get('notes', [NoteController::class, 'index']);
Route::get('notes/{id}', [NoteController::class, 'findById']);


//Todo Routes
Route::get('todos', [TodoController::class, 'index']);
Route::get('todos/{id}', [TodoController::class, 'findById']);



//Tag Routes
Route::get('tags', [TagController::class, 'index']);
Route::get('tags/{id}', [TagController::class, 'findById']);
