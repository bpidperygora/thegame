<?php

namespace App;

use Predis\Client as PredisClient;

class RedisClient
{
    private PredisClient $client;

    public function __construct()
    {
        $this->client = new PredisClient([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }

    public function set(string $key, string $value, $expire = null): void
    {
        $this->client->set($key, $value);
        if ($expire) {
            $this->client->expire($key, $expire);
        }
    }

    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    public function del(string $key): void
    {
        $this->client->del($key);
    }

    public function exists(string $key): bool
    {
        return (bool) $this->client->exists($key);
    }
    public function publish(string $channel, string $message): void
    {
        $this->client->publish($channel, $message);
    }

    /**
     * Subscribes to a Redis channel and listens for messages.
     * Note: This is a blocking operation.
     *
     * @param array $channels Array of channel names to subscribe to.
     * @param callable $callback Callback function to handle messages.
     */
    public function subscribe(array $channels, callable $callback): void
    {
        $loop = $this->client->pubSubLoop();
        $loop->subscribe($channels);

        try {
            /** @var \stdClass $message */
            foreach ($loop as $message) {
                if ($message->kind === 'message') {
                    $callback($message->payload);
                }
            }
        } catch (\Predis\Connection\ConnectionException $e) {
            // Log the error or handle it
            error_log("Redis connection error: " . $e->getMessage());
        }

        // Cleanup: unsubscribe and close the loop
        $loop->unsubscribe();
        unset($loop);
    }
}
