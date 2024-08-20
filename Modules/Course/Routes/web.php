<?php

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

Route::middleware(['auth'])->group(function () {
    // Route::prefix('course')->name('course.')->group(function() {
    //     Route::resource('/', CourseController::class);
    // });

    Route::resource('course', CourseController::class);
});
