<?php

use Illuminate\Http\Request;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('users', 'UserController@index');
Route::post('users', 'UserController@store');
Route::get('users/{user}', 'UserController@show');
Route::put('users/{user}', 'UserController@update');
Route::delete('users/{user}', 'UserController@destroy');


Route::get('tasks/{task_category_id?}', 'TaskController@index');
Route::post('tasks', 'TaskController@store');
Route::get('tasks/{task}', 'TaskController@show');
Route::put('tasks/{task}', 'TaskController@update');
Route::delete('tasks/{task}', 'TaskController@destroy');
Route::get('tasks/{task}/task_category', 'TaskController@taskCategory');


Route::get('task_categories', 'TaskCategoryController@index');
Route::post('task_categories', 'TaskCategoryController@store');
Route::get('task_categories/{taskCategory}', 'TaskCategoryController@show');
Route::put('task_categories/{taskCategory}', 'TaskCategoryController@update');
Route::delete('task_categories/{taskCategory}', 'TaskCategoryController@destroy');
Route::get('task_categories/{taskCategory}/tasks', 'TaskCategoryController@tasks');
