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
    return view('welcome');
});

Route::match(["get", "post"], "/neo4j", function(){
    return view("neo4j.search");
});
Route::match(["get", "post"], "/neo4j/search", "Neo4j\Neo4jController@search");
Route::match(["get", "post"], "/neo4j/recommend", "Neo4j\Neo4jController@recommend");
Route::get("/neo4j/detail", "Neo4j\Neo4jController@detail");
Route::post("/neo4j/rating", "Neo4j\Neo4jController@rating");