<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use App\Connector;

require './vendor/autoload.php';

// Create an event loop
$loop = LoopFactory::create();

// Instantiate the Connector with the event loop
$connector = new Connector($loop);

// Set up the WebSocket server with the Connector
$webSocket = new WsServer($connector);
$http = new HttpServer($webSocket);

// Create a socket server for the specified port
$socket = new Reactor('0.0.0.0:8081', $loop);

// Set up the IoServer with the HttpServer and the socket server
$server = new IoServer($http, $socket, $loop);

// Run the server
$server->run();
