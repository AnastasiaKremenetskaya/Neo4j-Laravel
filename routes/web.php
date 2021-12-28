<?php

use Illuminate\Support\Facades\Route;

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

//Route::resource('/movies', 'MovieController', [
//    'names' => [
//        'index' => 'movies.index',
//        'show' => 'movies.show',
//    ],
//    'parameters' => [
//        'movies' => 'title'
//    ]
//]);

Route::get("/", "MovieController@index");
Route::get("movies/{title}", "MovieController@show")->name('movies.show');

Route::get("people/{name}", "MovieController@showPerson")->name('people.show');
Route::get("people/{name}/edit", "MovieController@editPerson")->name('people.edit');
Route::put("people/{id}/update", "MovieController@updatePerson")->name('people.update');

Route::get("/search", "MovieController@search")->name('search');

//Route::get('/search', function () {
//    return view("search");
//});


//Route::match(["get", "post"], "/neo4j", function(){
//    return view("neo4j.search");
//});

Route::get("/recommend", "MovieController@recommend");
Route::post("/recommend", "MovieController@recommend");

Route::get("/detail", "MovieController@detail");
Route::post("/rating", "MovieController@rating");
