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

Route::get('/', function () {
    return view('index');
});

Route::get('/get_controllerlist.php', 'ToDoListController@select');
Route::post('/get_controllerlist.php', 'ToDoListController@select');

Route::post('/savecontent.php', 'ToDoListController@add_and_edit');

Route::get('/updatecontroller.php', 'ToDoListController@update');
Route::post('/updatecontroller.php', 'ToDoListController@update');