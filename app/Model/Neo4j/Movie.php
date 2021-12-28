<?php

namespace App\Model\Neo4j;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Illuminate\Support\Str;

class Movie
{

    private $client;
    const LABEL = "Movie";

    public function __construct()
    {
        $this->client = new Client("neo4j", 7474);
    }

    public function all()
    {
        $cypherStatement = "MATCH (m:Movie) RETURN m ORDER BY m.title";
        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = [
                "title" => $result->current()->title,
                "tagline" => $result->current()->tagline ?? '',
                "released" => $result->current()->released,
            ];
        }
        return $recommend;
    }

    public function allPeople()
    {
        $cypherStatement = "MATCH (p:Person) RETURN p.name ORDER BY p.name";
        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = $result->current();
        }
        return $recommend;
    }

    public function allPeopleWithIds()
    {
        $cypherStatement = "MATCH (p:Person) RETURN p.name, ID(p) ORDER BY p.name";
        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[$result[1]] = $result->current();
        }
        return $recommend;
    }

    public function findMoviesWithRelationsByTitle($title): array
    {
        $cypherStatement =
            "MATCH (m:Movie {title: {title}})<-[r]-(p:Person)"
            . " OPTIONAL MATCH (p)-[r2]->(movie:Movie)"
            . " RETURN type(r), p, type(r2), movie ORDER BY type(r), p.name";

        $cypherQuery = new Query($this->client, $cypherStatement, ["title" => $title]);
        $resultSet = $cypherQuery->getResultSet();

        $recommend = [];

        foreach ($resultSet as $result) {
            $relation = match ($result->current()) {
                'ACTED_IN' => 'Acted',
                'DIRECTED' => 'Directed',
                'null' => 'Found movies',
                default => Str::ucfirst(strtolower($result->current())),
            };
            $actor = $result[1]->getProperties()['name'];

//            dd($result[1]->getProperties());
            $recommend[$relation][$actor]['born'] = $result[1]->getProperties()['born'] ?? '';

            $relation2 = match ($result[2]) {
                'ACTED_IN' => 'Acted',
                'DIRECTED' => 'Directed',
                'null' => 'Found movies',
                default => Str::ucfirst(strtolower($result[2])),
            };

            $recommend[$relation][$actor]['movies'][$relation2][] = [
                "title" => $result[3]->getProperties()['title'],
                "tagline" => $result[3]->getProperties()['tagline'] ?? '',
                "released" => $result[3]->getProperties()['released'],
            ];
        }

        return $recommend;
    }

    public function searchMovieByTitleOrRelatedPersonName($query): array
    {
        $cypherStatement =
            "MATCH (p:Person)-[r]->(m:Movie)"
            . " WHERE lower(p.name) CONTAINS lower({query})"
            . " RETURN m as movie, type(r) as relation"
            . " UNION"
            . " MATCH (m:Movie)"
            . " WHERE lower(m.title) CONTAINS lower({query})"
            . " RETURN m as movie, 'null' as relation ORDER BY relation";

        $cypherQuery = new Query($this->client, $cypherStatement, ["query" => $query]);

        $resultSet = $cypherQuery->getResultSet();

        $recommend = [];

        foreach ($resultSet as $result) {
            $relation = match ($result[1]) {
                'ACTED_IN' => 'Acted in',
                'DIRECTED' => 'Directed',
                'null' => 'Found movies',
                default => ucfirst($result[1]),
            };
            $recommend[$relation][] = [
                "title" => $result->current()->title,
                "tagline" => $result->current()->tagline ?? '',
                "released" => $result->current()->released,
            ];
        }
        return $recommend;
    }

    public function findPerson($name): array
    {
        $cypherStatement =
            "MATCH (p:Person {name: {name}})-[r]->(m:Movie)"
            . " RETURN ID(p), p, type(r), m ORDER BY type(r), p.name";

        $cypherQuery = new Query($this->client, $cypherStatement, ["name" => $name]);
        $resultSet = $cypherQuery->getResultSet();

        $recommend = [];

        foreach ($resultSet as $result) {
            $recommend["id"] = $result[0];
            $recommend["born"] = $result[1]->getProperties()['born'];
            $recommend["name"] = $result[1]->getProperties()['name'];

            $relation2 = match ($result[2]) {
                'ACTED_IN' => 'Acted',
                'DIRECTED' => 'Directed',
                'null' => 'Found movies',
                default => Str::ucfirst(strtolower($result[2])),
            };

            $recommend['movies'][$relation2][] = [
                "title" => $result[3]->getProperties()['title'],
                "tagline" => $result[3]->getProperties()['tagline'] ?? '',
                "released" => $result[3]->getProperties()['released'],
            ];
        }

        return $recommend;
    }

    public function updatePerson($data): string
    {
        $transaction = $this->client->beginTransaction();
        $cypherStatement =
            "MATCH (p:Person)"
            . " WHERE ID(p) = toInt({id})"
            . " SET p.born = toInt({born})"
            . " SET p.name = {name} RETURN p.name";

        $cypherQuery = new Query($this->client, $cypherStatement, [
            "name" => $data['name'],
            'born' => $data['born'],
            'id' => $data['id'],
        ]);
        $transaction->addStatements($cypherQuery);
        $transaction->commit();

        return $cypherQuery->getResultSet()[0]->current();
    }

    public function deleteMovie($query)
    {
        $transaction = $this->client->beginTransaction();
        $cypherStatement =
            "MATCH (m:Movie {title: {title}})"
            . " DETACH DELETE m";

        $cypherQuery = new Query($this->client, $cypherStatement, [
            "title" => $query,
        ]);
        $transaction->addStatements($cypherQuery);
        $transaction->commit();
    }

    public function storeMovie($data)
    {
        $cypherStatement =
            "CREATE (m:Movie {title: {title}, released: {released}, tagline: {tagline}})"
            . " RETURN m.title";

        $cypherQuery = new Query($this->client, $cypherStatement, [
            "title" => $data['title'],
            "released" => $data['released'],
            "tagline" => $data['tagline'],
        ]);
        $newMovie = $cypherQuery->getResultSet()[0]->current();

        //Set relations
        foreach ($data['relations'] as $relation => $people) {
            foreach ($people as $person) {
                $cypherStatement =
                    "MATCH (m:Movie {title: {title}}), (p:Person)"
                    . " WHERE ID(p) = toInt({name})"
                    . " CREATE (p)-[r:" . $relation . "]->(m)";

                (new Query($this->client, $cypherStatement, [
                    "title" => $newMovie,
                    "name" => $person,
                ]))->getResultSet();
            }
        }

        return $newMovie ?? $data['title'];
    }


    //1. Выбрать всех продюсеров, которые написали сценарий хотя бы для одного своего фильма.
    // При этом в фильме должно быть минимум три актера
    public function report1()
    {
        $cypherStatement = "MATCH (p:Person)-[r:WROTE]->(m:Movie)<-[r2:ACTED_IN]-(:Person)"
            . " WITH p,m, count(r2) as countr2"
            . " WHERE countr2 > 3 RETURN p, m";

        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[$result[0]->getProperties()['name']]['born'] = $result[0]->getProperties()['born'];
            $recommend[$result[0]->getProperties()['name']]['movies'][] = [
                "title" => $result[1]->getProperties()['title'],
                "tagline" => $result[1]->getProperties()['tagline'] ?? '',
                "released" => $result[1]->getProperties()['released'],
            ];
        }
        return $recommend;
    }

    //2. Выбрать фильмы, которые рекомендуют люди, обозревшие Cloud Atlas. Отсортировать по количеству
    public function report2()
    {
        $cypherStatement = "MATCH (m:Movie {title: 'Cloud Atlas'})<-[:REVIEWED]-(p:Person)-[:REVIEWED]->(rec:Movie)"
            . " RETURN rec, COUNT(*) AS usersWhoAlsoWatched"
            . " ORDER BY usersWhoAlsoWatched DESC LIMIT 25";

        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = $result[0]->getProperties();
        }
        return $recommend;
    }

    //3. Выбрать фильмы, которые Джессика Томпсон оценила выше своих средних оценок
    public function report3()
    {
        $cypherStatement = "MATCH (u:Person {name: 'Jessica Thompson'})"
            . " MATCH (u)-[r:REVIEWED]->(m:Movie)"
            . " WITH u, avg(r.rating) AS average"
            . " MATCH (u)-[r:REVIEWED]->(m:Movie)"
            . " WHERE r.rating > average"
            . " RETURN m";

        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = $result[0]->getProperties();
        }
        return $recommend;
    }

    //4. Выбрать фильм с наименьшим рейтингом
    public function report4()
    {
        $cypherStatement = "MATCH (u:Person)-[r:REVIEWED]->(m:Movie)"
            . " RETURN m, r.rating ORDER BY r.rating ASC LIMIT 1";

        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = $result[0]->getProperties();
            $res = $result[1];
        }
        return [$recommend, $res];
    }

    //5. Выбрать фильм с наибольшим рейтингом
    public function report5()
    {
        $cypherStatement = "MATCH (u:Person)-[r:REVIEWED]->(m:Movie)"
            . " RETURN m, r.rating ORDER BY r.rating DESC LIMIT 1";

        $cypherQuery = new Query($this->client, $cypherStatement);
        $resultSet = $cypherQuery->getResultSet();
        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = $result[0]->getProperties();
            $res = $result[1];
        }
        return [$recommend, $res];
    }
}
