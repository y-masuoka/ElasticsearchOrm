<?php

namespace Osushi\ElasticsearchOrm;

use Elasticsearch\Client;

class Index
{
    protected $index;

    protected $callback;

    protected $connection;

    protected $shards = 5;

    protected $replicas = 0;

    protected $mappings = [];

    public function __construct(string $index, $callback = null)
    {
        $this->index = $index;
        $this->callback = $callback;
    }

    public function setConnection(Client $connection)
    {
        $this->connection = $connection;
    }

    public function create()
    {
        if (is_callback_function($this->callback)) {
            $this->callback($this);
        }

        $params = [
            'index' => $this->index,
            'body' => [
                "settings" => [
                    'number_of_shards' => $this->shards,
                    'number_of_replicas' => $this->replicas,
                ],
            ],
        ];

        if (count($this->mappings)) {
            $params["body"]["mappings"] = $this->mappings;
        }

        return $this->connection->indices()->create($params);
    }

    public function drop()
    {
        $params = [
            'index' => $this->index,
        ];

        return $this->connection->indices()->delete($params);
    }

    public function exists()
    {
        $params = [
            'index' => $this->index,
        ];

        return $this->connection->indices()->exists($params);
    }

    public function shards(int $shards)
    {
        $this->shards = $shards;
        return $this;
    }

    public function replicas(int $replicas)
    {
        $this->replicas = $replicas;
        return $this;
    }

    public function mappings(array $mappings = [])
    {
        $this->mappings = $mappings;
        return $this;
    }
}