<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('login', [RegisterController::class, 'loginAccount']);
Route::post('register', [RegisterController::class, 'registerAccount']);
Route::post('forgot_password', [RegisterController::class, 'forgotPassword']);
Route::post('verify_otp', [RegisterController::class, 'verifyOtp']);
Route::post('change_password', [RegisterController::class, 'changePassword']);
// Route::get('verify-email/{token?}', [RegisterController::class, 'verifyUserEmail'])->name('email_verify');
Route::get('logout', [RegisterController::class, 'logoutUser']);


/*
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('forgot_password', [RegisterController::class, 'forgotPassword']);
*/

Route::middleware('auth:api')->group( function () {

    // Route::post('logout', [RegisterController::class, 'logoutProfile']);

    Route::get('get_profile', [RegisterController::class, 'get_profile']);
    // Route::post('update_profile', [RegisterController::class, 'updateProfile']);

    //resouce routes
    // Route::resource('services', ServiceController::class);
});
