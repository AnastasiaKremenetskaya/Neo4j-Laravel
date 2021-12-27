<?php
require('vendor/autoload.php'); // or your custom autoloader

// Connecting to the default port 7474 on localhost
$client = new Everyman\Neo4j\Client();

// Connecting to a different port or host
$client = new Everyman\Neo4j\Client("neo4j", 7474);

// Connecting using HTTPS and Basic Auth
$client = new Everyman\Neo4j\Client();
$client->getTransport()
    ->setAuth("neo4j", "1234567q");

// Test connection to server
print_r($client->getServerInfo());
