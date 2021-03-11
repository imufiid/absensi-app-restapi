<?php

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

Route::group([
    "namespace" => 'Auth',
    "prefix"    => "auth"
], function () {
    Route::post('register', 'RegistrationController');
    Route::post('login', 'LoginController');
    Route::post('logout', 'LogoutController');
});

Route::group([
    "namespace" => 'Task',
    "prefix"    => 'task'
], function() {
    Route::get('/', 'TaskController@index');
    Route::get('/show', 'TaskController@show');
    Route::put('/mark', 'TaskController@markComplete');
    Route::post('/store', 'TaskController@store');
});

Route::group([
    "namespace" => 'Attendance',
    "prefix"    => 'attendance'
], function () {
    Route::get("/", 'AttendanceController@index');
    Route::post("/come", 'AttendanceController@comes');
    Route::put("/gohome", 'AttendanceController@gohome');
    Route::get("/show", 'AttendanceController@show');
});

Route::group([
    "namespace" => 'User',
    "prefix"    => 'user'
], function() {
    Route::get('/', 'UserController@index');
    Route::post('/save', 'UserController@update');
});

Route::group([
    "namespace" => 'Employee',
    "prefix"    => 'employee'
], function() {
    Route::get('/', 'EmployeeController@index');
});
