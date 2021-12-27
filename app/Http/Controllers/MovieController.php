<?php

namespace App\Http\Controllers;

use App\Model\Neo4j\Movie;
use Illuminate\Http\Request;


class MovieController extends Controller
{
    /**
     * @var Movie
     */
    private $movie;

    public function __construct()
    {
        $this->movie = new Movie();
    }

    public function index()
    {
        $data = $this->movie->all();

        return view("movies.list", ["movies" => $data]);
    }

    public function show(Request $request)
    {
        $data = $this->movie->findMoviesWithRelationsByTitle($request['title']);

        return view("people.list", ["people" => $data, 'movie' => $request['title']]);
    }

    public function search(Request $request)
    {
        $query = $request['search'];

        if (empty($query)) {
            return $this->index();
        }

        $data = $this->movie->searchByMovieOrPerson($query);

        return view("movies.search_result", [
            "movies" => $data,
            "query" => $query
        ]);
    }

    public function recommend(Request $request)
    {

        $recommend = [];
        if ($request->has("movie_id")) {
            $movieId = $request->input("movie_id");
//        dd($movieId);

            $model = new Movie();
            $recommend = $model->findRecommendMovie($movieId);
        }

        return view("recommend")
            ->with("recommends", $recommend);
    }

    public function rating(Request $request)
    {
        $nodeProp = null;
        if ($request->has(["movie_id", "rating"])) {
            $movieId = $request->input("movie_id");
            $rate = $request->input("rating");
            $model = new Movie();
            $model->addPropertyWithTransaction($movieId, $rate);
            $nodeProp = $model->findPropertiesById($movieId);
        }

        return view("detail")->with("nodeProp", $nodeProp);
    }
}
