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

Route::get('/', 'IndexController@index')->name('index');
Route::get('/result', 'IndexController@result')->name('result');
Route::get('/checkAll', 'IndexController@checkAll')->name('checkAll');

Route::post('/save', 'IndexController@save')->name('save');
