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

            $recommend[$relation][$actor]['born'] = $result[1]->getProperties()['born'];

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

    public function deleteMovie($data)
    {
        $transaction = $this->client->beginTransaction();
        $cypherStatement =
            "MATCH (m:Movie {title: {title})"
            . " DELETE m";

        $cypherQuery = new Query($this->client, $cypherStatement, [
            "title" => $data['title'],
        ]);
        $transaction->addStatements($cypherQuery);
        $transaction->commit();
    }




    public function findNodesByReleased($released)
    {
        $label = $this->client->makeLabel(self::LABEL);
        $nodes = $label->getNodes("released", $released);

        $movies = [];
        foreach ($nodes as $node) {
            $movies[] = [
                "id" => $node->getId(),
                "title" => $node->getProperty("title"),
                "released" => $node->getProperty("released"),
            ];
        }
        return $movies;
    }

    public function findRecommendMovie($movieNodeId)
    {
        $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
        $cypherStatement =
            "MATCH (m:Movie)<-[:ACTED_IN]-(p:Person)-[:ACTED_IN]->(n:Movie) "
            . " WHERE ID(m) = toInt({id}) AND ID(n) <> toInt({id}) "
            . " RETURN ID(n) as id, p.name AS name, n.title AS title ORDER BY name ";

        $cypherQuery = new Query($this->client, $cypherStatement, ["id" => $id]);
        $resultSet = $cypherQuery->getResultSet();

        $recommend = [];
        foreach ($resultSet as $result) {
            $recommend[] = [
                "id" => $result["id"],
                "name" => $result["name"],
                "title" => $result["title"]
            ];
        }
        return $recommend;
    }

    public function findPropertiesById($movieNodeId)
    {
        $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
        $node = $this->client->getNode($id);
        $props = $node->getProperties();
        $props += ["id" => $id];
        if (in_array("rating", $props, true) === false) {
            $props += ["rating" => 0];
        }
        return $props;
    }

    public function addPropertyWithTransaction($movieNodeId, $rating)
    {
        $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
        $rate = is_int($rating) ? $rating : (int)$rating;
        $transaction = $this->client->beginTransaction();
        try {
            $cypherStatement = "MATCH (m:Movie) WHERE ID(m) = {id} SET m.rating = {rating}";
            $query = new Query($this->client, $cypherStatement, ["id" => $id, "rating" => $rate]);
            $transaction->addStatements($query);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
        }
    }
}
