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

Route::get('/', function () {
    return view("search");
});


//Route::match(["get", "post"], "/neo4j", function(){
//    return view("neo4j.search");
//});
Route::post("/search", "Neo4j\Neo4jController@search");

Route::get("/recommend", "Neo4j\Neo4jController@recommend");
Route::post("/recommend", "Neo4j\Neo4jController@recommend");

Route::get("/detail", "Neo4j\Neo4jController@detail");
Route::post("/rating", "Neo4j\Neo4jController@rating");
