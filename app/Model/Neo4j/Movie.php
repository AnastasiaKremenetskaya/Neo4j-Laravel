<?php

namespace App\Model\Neo4j;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;

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
                "tagline" => $result->current()->tagline,
                "released" => $result->current()->released,
            ];
        }
        return $recommend;
    }

    public function findMoviesWithRelationsByTitle($title): array
    {
        $cypherStatement =
            "MATCH (m:Movie)<-[:ACTED_IN]-(p:Person)-[:ACTED_IN]->(movie:Movie)"
            ." WHERE m.title = {title}"
            . " RETURN p, movie ORDER BY p.name ";

        $cypherQuery = new Query($this->client, $cypherStatement, ["title" => $title]);
        $resultSet = $cypherQuery->getResultSet();

        $recommend = [];

        foreach ($resultSet as $result) {
            $actor = $result[0]->getProperties()['name'];
            if (!array_key_exists($actor, $recommend)) {
                $recommend[$actor] = [
                    'born' => $result[0]->getProperties()['born'],
                ];
            }
            $movie = [
                'title' => $result[1]->getProperties()['title'],
                'tagline' => $result[1]->getProperties()['tagline'] ?? '',
                'released' => $result[1]->getProperties()['released'],
            ];

            $recommend[$result[0]->getProperties()['name']]['movies'][] = $movie;
        }
        return $recommend;
    }

    public function searchByMovieOrPerson($query)
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
            $relation = match($result[1]) {
                'ACTED_IN' => 'Acted in',
                'DIRECTED' => 'Directed',
                'null' => 'Found movies',
                default => ucfirst($result[1]),
            };
            $recommend[$relation][] = [
                "title" => $result->current()->title,
                "tagline" => $result->current()->tagline,
                "released" => $result->current()->released,
            ];
        }
        return $recommend;
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
