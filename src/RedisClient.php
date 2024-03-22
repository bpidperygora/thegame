<?php

namespace App;

use Predis\Client as PredisClient;
use Predis\Connection\ConnectionException;

class RedisClient
{
    private PredisClient $client;

    /**
     * Constructs a new RedisClient instance.
     */
    public function __construct()
    {
        $this->client = new PredisClient([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }

    /**
     * Sets a value in Redis.
     *
     * @param string $key The key under which to store the value.
     * @param string $value The value to store.
     * @param int|null $expire The expiration time in seconds.
     */
    public function set(string $key, string $value, ?int $expire = null): void
    {
        $this->client->set($key, $value);
        if ($expire !== null) {
            $this->client->expire($key, $expire);
        }
    }

    /**
     * Retrieves a value from Redis.
     *
     * @param string $key The key whose value to retrieve.
     * @return string|null The value or null if the key does not exist.
     */
    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    /**
     * Deletes a key from Redis.
     *
     * @param string $key The key to delete.
     */
    public function del(string $key): void
    {
        $this->client->del($key);
    }

    /**
     * Checks if a key exists in Redis.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public function exists(string $key): bool
    {
        return (bool) $this->client->exists($key);
    }

    /**
     * Publishes a message to a Redis channel.
     *
     * @param string $channel The channel to publish to.
     * @param string $message The message to publish.
     */
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
            foreach ($loop as $message) {
                /** @var \stdClass $message */
                if ($message->kind === 'message') {
                    $callback($message->payload);
                }
            }
        } catch (ConnectionException $e) {
            // Log the error or handle it appropriately
            error_log("Redis connection error: " . $e->getMessage());
        }
        // Cleanup: unsubscribe and close the loop
        $loop->unsubscribe();
        unset($loop);
    }
}
