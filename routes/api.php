<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookController;

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


Route::prefix('book')->group(function(){
    Route::get('index', [BookController::class, 'index']);
    Route::post('store', [BookController::class, 'store']);
    Route::put('update/{id}', [BookController::class, 'update']);
    Route::get('show/{id}', [BookController::class, 'show']);
    Route::delete('delete/{id}', [BookController::class, 'delete']);
});

//Authentication is not required for these endpoints
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//Authentication is required for these endpoints (apply middleware auth:sanctum)
Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::get('userProfile', [AuthController::class, 'userProfile']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::put('changePassword', [AuthController::class, 'changePassword']);


    Route::post('addBookReview', [BookController::class, 'addBookReview']);
    Route::put('updateBookReview/{id}', [BookController::class, 'updateBookReview']);
});


/*
php artisan make:migration create_categories_table
php artisan make:model Category

php artisan make:migration create_editorials_table
php artisan make:model Editorial

php artisan make:migration create_books_table
php artisan make:model Book

php artisan make:migration create_authors_table
php artisan make:model Author

php artisan make:migration create_authors_books_table


php artisan make:controller BookController
*/

