<?php

namespace Matchish\ScoutElasticSearch\Jobs\Stages;

use Matchish\ScoutElasticSearch\Creator\ProxyClient;
use Matchish\ScoutElasticSearch\ElasticSearch\DefaultAlias;
use Matchish\ScoutElasticSearch\ElasticSearch\Index;
use Matchish\ScoutElasticSearch\ElasticSearch\Params\Indices\Create;
use Matchish\ScoutElasticSearch\ElasticSearch\WriteAlias;
use Matchish\ScoutElasticSearch\Searchable\ImportSource;
use Illuminate\Support\Arr;

/**
 * @internal
 */
final class CreateWriteIndex
{
    /**
     * @var ImportSource
     */
    private $source;
    /**
     * @var Index
     */
    private $index;

    private string $name;

    /**
     * @param  ImportSource  $source
     * @param  Index  $index
     */
    public function __construct(ImportSource $source, Index $index, string $name)
    {
        $this->source = $source;
        $this->index = $index;
        $this->name = $name;
    }

    public function handle(ProxyClient $elasticsearch): void
    {
        $source = $this->source;
        $config = $this->index->config();

        $this->index->addAlias(new WriteAlias(new DefaultAlias($source->searchableAs())));

        Arr::forget($config, 'aliases');

        $params = new Create(
            $this->name,
            $config
        );

        $elasticsearch->indices()->create($params->toArray());
    }

    public function title(): string
    {
        return 'Create write index';
    }

    public function estimate(): int
    {
        return 1;
    }
}
