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

/*Route::get('/', function () {
    return view('welcome');
});
*/
Auth::routes();


Route::get('/admin', 'AdminController@index')->name('dashboard');

Route::get('/', 'User\AnnoncesController@index')->name('home');
Route::get('/annonces/{id}', 'User\AnnoncesController@show')->name('detailAnnonce');
Route::resource('/admin/annonces', 'Admin\AnnoncesController', ['as'=>'admin']);
Route::post('/admin/annonces/visibility/{id}', 'Admin\AnnoncesController@visibility')->name('admin.annonces.visibility');

Route::get('/recrutements', 'User\RecrutementsController@index')->name('recrutements');
Route::get('/recrutements/{id}', 'User\RecrutementsController@show')->name('detailRecrutement');
Route::resource('/admin/recrutements', 'Admin\RecrutementsController', ['as'=>'admin']);
Route::post('/admin/recrutements/visibility/{id}', 'Admin\RecrutementsController@visibility')->name('admin.recrutements.visibility');

Route::resource('/admin/publicites', 'Admin\PublicitesController', ['as'=>'admin']);
Route::get('/publicites/{id}', 'User\PublicitesController@show')->name('detailPublicite');
Route::post('/admin/publicites/visibility/{id}', 'Admin\PublicitesController@visibility')->name('admin.publicites.visibility');

Route::resource('/admin/annonceurs', 'Admin\AnnonceursController', ['as'=>'admin']);
Route::post('/admin/annonceurs/active/{id}', 'Admin\AnnonceursController@active')->name('admin.annonceurs.active');

Route::get('/contact', 'User\AnnoncesController@contact')->name('contact');



