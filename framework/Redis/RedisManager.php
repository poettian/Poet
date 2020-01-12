<?php


namespace Poet\Redis;


class RedisManager
{
    protected $app;

    protected $connections;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * get redis specified connection
     *
     * @param  null  $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->createClient($name);
    }

    /**
     * get redis client
     *
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    protected function createClient($name)
    {
        $config = $this->app['config']->get('redis');
        if (! isset($config[$name])) {
            throw new \Exception("redis connection:{$name} not found.");
        }
        $config = $config[$name];
        $driver = 'create' . ucfirst($config['driver']) . 'Client';

        return $this->{$driver}($config);
    }

    protected function createSingleClient($config)
    {
        $client = new \Redis();

        $persistent = $config['persistent'] ?? false;

        $parameters = [
            $config['host'],
            $config['port'],
            $config['timeout'] ?? 0.0,
            $persistent ? ($config['persistent_id'] ?? null) : null,
            $config['retry_interval'] ?? 0,
        ];

        $client->{($persistent ? 'pconnect' : 'connect')}(...$parameters);

        if (! empty($config['password'])) {
            $client->auth($config['password']);
        }

        if (! empty($config['database'])) {
            $client->select($config['database']);
        }

        $this->setOption($client, $config['options'] ?? []);

        return $client;
    }

    protected function createSentinelClient()
    {

    }

    protected function setOption($client, array $options)
    {
        foreach ($options as $key => $value) {
            $client->setOption($key, $value);
        }
    }

    /**
     * Pass methods onto the default Redis connection.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }
}