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

        $data = $this->movie->searchMovieByTitleOrRelatedPersonName($query);

        return view("movies.search_result", [
            "movies" => $data,
            "query" => $query
        ]);
    }

    public function showPerson(Request $request)
    {
        $query = $request['name'];

        $data = $this->movie->searchMovieByTitleOrRelatedPersonName($query);

        return view("movies.search_result", [
            "movies" => $data,
            "query" => $query,
            "showEditPersonButton" => true
        ]);
    }

    public function editPerson(Request $request)
    {
        $query = $request['name'];

        $data = $this->movie->findPerson($query);

        if (!empty($data))
            return view("people.edit", [
                "person" => $data,
            ]); else {
            return redirect('/');
        }
    }

    public function deleteMovie(Request $request)
    {
        $query = $request['title'];

        $this->movie->deleteMovie($query);

        return redirect('/');
    }

    public function updatePerson(Request $request)
    {
        $name = $this->movie->updatePerson($request);

        $data = $this->movie->findPerson($name);

        return view("people.edit", [
            "person" => $data,
        ]);
    }

    public function createMovie()
    {
        $people = $this->movie->allPeopleWithIds();

        return view("movies.create", ['people' => $people]);
    }

    public function storeMovie(Request $request)
    {
        $newMovie = $this->movie->storeMovie($request->all());

        $data = $this->movie->searchMovieByTitleOrRelatedPersonName($newMovie);

        return view("movies.search_result", [
            "movies" => $data,
            "query" => $newMovie
        ]);
    }

    //1. Выбрать всех продюсеров, которые написали сценарий хотя бы для одного фильма.
    // При этом в фильме должно быть минимум три актера
    public function report1()
    {
        $data = $this->movie->report1();

        return view("reports.1", [
            "people" => $data,
        ]);
    }

    //2. Выбрать фильмы, которые рекомендуют люди, посмотревшие Cloud Atlas. Отсортировать по количеству
    public function report2()
    {
        $data = $this->movie->report2();

        return view("reports.2", [
            "movies" => $data,
        ]);
    }

    //3. Выбрать фильмы, которые Джессика Томпсон оценила выше своих средних оценок
    public function report3()
    {
        $data = $this->movie->report3();

        return view("reports.3", [
            "movies" => $data,
        ]);
    }

    //4. Выбрать фильм с самым низким рейтингом из существующих
    public function report4()
    {
        $data = $this->movie->report4();

        return view("reports.5", [
            "movies" => $data[0],
            "rating" => $data[1],
        ]);
    }

    //4. Выбрать фильм с самым низким рейтингом из существующих
    public function report5()
    {
        $data = $this->movie->report5();

        return view("reports.4", [
            "movies" => $data[0],
            "rating" => $data[1],
        ]);
    }
}
