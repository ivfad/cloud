<?php

namespace Core\Cache;

use Core\Helpers\SingletonTrait;
use Predis\Client;

class RedisService
{
    use SingletonTrait;

    private Client $client;

    protected function __construct()
    {
        $this->client = new Client([
            'scheme'   => getenv("REDIS_SCHEME"),
            'host'     => getenv("REDIS_HOST"),
            'port'     => getenv("REDIS_PORT"),
            'password' => getenv("REDIS_PASSWORD"),
            'database' => getenv("REDIS_DATABASE"),
        ]);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->client->set($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->client->get($key);
    }

    /**
     * @param string $key
     * @param int $ttl
     * @param string $value
     * @return void
     */
    public function setEx(string $key, int $ttl, string $value): void
    {
        $this->client->setex($key, $ttl, $value);
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->client->del([$key]);
    }

}