<?php
namespace App\Http\Controllers\Neo4j;

use App\Http\Controllers\Controller;
use App\Model\Neo4j\Movie;
use Illuminate\Http\Request;


class Neo4jController extends Controller{
  
  public function search(Request $request){

    $data = [];
    $released = null;

    if($request->has("released")){
      $released = $request->input("released");
      $model = new Movie();
      $data = $model->findNodesByReleased( $released );
    }

    return view("neo4j.search")
      ->with("movies", $data )
      ->with("formReleased", $released );
  }

  public function recommend(Request $request){

    $recommend = [];
    if($request->has("movie_id")){
      $movieId = $request->input("movie_id");
      $model = new Movie();
      $recommend = $model->findRecommendMovie($movieId);
    }

    return view("neo4j.recommend")
      ->with("recommends", $recommend);
  }

  public function detail(Request $request){
    $nodeProp = null;
    if($request->has("movie_id")){
      $movieId = $request->input("movie_id");
      $model = new Movie();
      $nodeProp = $model->findPropertiesById($movieId);
    }
    return view("neo4j.detail")->with("nodeProp", $nodeProp);
  }

  public function rating(Request $request){
    $nodeProp = null;
    if($request->has(["movie_id", "rating"])){
      $movieId = $request->input("movie_id");
      $rate = $request->input("rating");
      $model = new Movie();
      $model->addPropertyWithTransaction($movieId, $rate);
      $nodeProp = $model->findPropertiesById($movieId);
    }

    return view("neo4j.detail")->with("nodeProp", $nodeProp);
  }
}