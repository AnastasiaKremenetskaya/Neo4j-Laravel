<?php

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;
use Everyman\Neo4j\Cypher\Query;
use Illuminate\Database\Seeder;

class MoviesActorsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Everyman\Neo4j\Exception
     */
    public function run()
    {

        $client = new Client("neo4j", 7474);
//        $actors = new NodeIndex($client, 'actors');
        $transaction = $client->beginTransaction();
//        try {
            $cypherStatement = 'LOAD CSV FROM "file:///Users/gvterechov/RubymineProjects/Neo4j-Laravel/database/data/movies.csv" AS row RETURN row';
            $cypherQuery = new Query($client, $cypherStatement);
            $transaction->addStatements($cypherQuery);
            $transaction->commit();
//        } catch (\Exception $e) {
//            $transaction->rollback();
//        }


        $keanu = $client->makeNode()->setProperty('name', 'Keanu Reeves')->save();
//        $laurence = $client->makeNode()->setProperty('name', 'Laurence Fishburne')->save();
//        $jennifer = $client->makeNode()->setProperty('name', 'Jennifer Connelly')->save();
//        $kevin = $client->makeNode()->setProperty('name', 'Kevin Bacon')->save();
//
//        $actors->add($keanu, 'name', $keanu->getProperty('name'));
//        $actors->add($laurence, 'name', $laurence->getProperty('name'));
//        $actors->add($jennifer, 'name', $jennifer->getProperty('name'));
//        $actors->add($kevin, 'name', $kevin->getProperty('name'));
//
//        $matrix = $client->makeNode()->setProperties(['title' => 'The Matrix', 'released' => '2003'])->save();
//        $higherLearning = $client->makeNode()->setProperties(['title' => 'Higher Learning', 'released' => '2010'])->save();
//        $mysticRiver = $client->makeNode()->setProperties(['title' => 'Mystic River', 'released' => '2003'])->save();
//
//        $keanu->relateTo($matrix, 'ACTED_IN')->save();
//        $laurence->relateTo($matrix, 'ACTED_IN')->save();
//
//        $laurence->relateTo($higherLearning, 'ACTED_IN')->save();
//        $jennifer->relateTo($higherLearning, 'ACTED_IN')->save();
//
//        $laurence->relateTo($mysticRiver, 'ACTED_IN')->save();
//        $kevin->relateTo($mysticRiver, 'ACTED_IN')->save();

//// Find all actors in a movie
//} else if ($cmd == 'actors') {
//
//    if(!empty($argv[2])) {
//        $movie = implode(" ", array_slice($argv,2));
//    } else {
//        $movie = "The Matrix";
//    }
//
//    $queryTemplate = "START actor=node:actors('name:*') ".
//        "MATCH (actor) -[:IN]- (movie)".
//        "WHERE movie.title = {title}".
//        "RETURN actor";
//    $query = new Cypher\Query($client, $queryTemplate, array('title'=>$movie));
//    $result = $query->getResultSet();
//
//    echo "Found ".count($result)." actors:\n";
//    foreach($result as $row) {
//        echo "  ".$row['actor']->getProperty('name')."\n";
//    }
//}
    }
}
