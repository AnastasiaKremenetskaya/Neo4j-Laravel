<?php
namespace App\Model\Neo4j;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;

class Movie{

  private $client;
  const LABEL = "Movie";

  public function __construct(){
    $this->client = new Client("neo4j", 7474);
    $this->client->getTransport()->setAuth("neo4j", "****");
  }

  public function findNodesByReleased($released){
    $label = $this->client->makeLabel(self::LABEL);
    $nodes = $label->getNodes("released", $released);

    $movies = [];
    foreach($nodes as $node){
      $movies[] = [
        "id" => $node->getId(),
        "title" => $node->getProperty("title"),
        "released" => $node->getProperty("released"),
      ];
    }
    return $movies;
  }

  public function findRecommendMovie($movieNodeId){
    $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
    $cypherStatement = 
      "MATCH (m:Movie)<-[:ACTED_IN]-(p:Person)-[:ACTED_IN]->(n:Movie) "
      . " WHERE ID(m) = toInt({id}) AND ID(n) <> toInt({id}) "
      . " RETURN ID(n) as id, p.name AS name, n.title AS title ORDER BY name ";
    
    $cypherQuery = new Query($this->client, $cypherStatement, ["id"=>$id]);
    $resultSet = $cypherQuery->getResultSet();

    $recommend = [];
    foreach($resultSet as $result){
      $recommend[] = [
        "id" => $result["id"],
        "name" => $result["name"],
        "title" => $result["title"]
      ];
    }
    return $recommend;
  }

  public function findPropertiesById($movieNodeId){
    $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
    $node = $this->client->getNode($id);
    $props = $node->getProperties();
    $props += ["id" => $id];
    if(in_array("rating", $props, true) === false ){
      $props += ["rating" => 0];
    }
    return $props;
  }

  public function addPropertyWithTransaction($movieNodeId, $rating){
    $id = is_int($movieNodeId) ? $movieNodeId : (int)$movieNodeId;
    $rate = is_int($rating) ? $rating : (int)$rating;
    $transaction = $this->client->beginTransaction();
    try{
      $cypherStatement = "MATCH (m:Movie) WHERE ID(m) = {id} SET m.rating = {rating}";
      $query = new Query($this->client, $cypherStatement, ["id"=>$id, "rating"=>$rate]);
      $transaction->addStatements($query);
      $transaction->commit();
    }catch(\Exception $e){
      $transaction->rollback();
    }
  }
}