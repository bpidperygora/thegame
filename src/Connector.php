<?php

namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Clue\React\Redis\Factory;
use Clue\React\Redis\Client as RedisClient;
use SplObjectStorage;

class Connector implements MessageComponentInterface
{
    protected $clients;
    protected $clientInfo;
    private $loop;

    public function __construct($loop)
    {
        $this->clients = new SplObjectStorage();
        $this->clientInfo = [];
        $this->loop = $loop;
        $this->subscribeToLogoutChannel();
        echo "WebSocket server started.\n";
    }


    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        // Handle registration messages
        if ($data['action'] === 'register' && isset($data['id'])) {
            $this->clientInfo[$from->resourceId] = $data['id'];
            echo "Connection {$from->resourceId} registered with ID {$data['id']}\n";
        }

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        if (isset($this->clientInfo[$conn->resourceId])) {
            unset($this->clientInfo[$conn->resourceId]);
        }
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Triggers the logout action for connections associated with a given ID.
     * @param string $id The random ID associated with the user/session.
     */
    public function triggerLogoutById($id)
    {
        foreach ($this->clientInfo as $resourceId => $clientId) {
            if ($clientId === $id) {
                // Find the connection object
                foreach ($this->clients as $client) {
                    if ($client->resourceId == $resourceId) {
                        // Send a logout message to this client
                        $client->send(json_encode(['action' => 'logout']));
                        echo "Logout triggered for connection {$client->resourceId}\n";
                    }
                }
            }
        }
    }
    private function subscribeToLogoutChannel()
    {
        $factory = new Factory($this->loop);
        $factory->createClient('localhost:6379')->then(function (RedisClient $client) {
            $client->subscribe('logout_channel');
            $client->on('message', function ($channel, $message) {
                echo "Message on '{$channel}': {$message}\n";
                $data = json_decode($message, true);
                if (isset($data['id'])) {
                    $this->triggerLogoutById($data['id']);
                }
            });
        });
    }
}
