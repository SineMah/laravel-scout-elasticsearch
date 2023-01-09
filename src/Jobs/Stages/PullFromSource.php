<?php

namespace Matchish\ScoutElasticSearch\Jobs\Stages;

use Illuminate\Support\Collection;
use Matchish\ScoutElasticSearch\Searchable\ImportSource;

/**
 * @internal
 */
final class PullFromSource
{
    /**
     * @var ImportSource
     */
    private $source;
    private string $name;

    /**
     * @param  ImportSource  $source
     */
    public function __construct(ImportSource $source, string $name)
    {
        $this->source = $source;
        $this->name = $name;
    }

    public function handle(): void
    {
        $results = $this->source->get()->filter->shouldBeSearchable();
        if (! $results->isEmpty()) {
            $results->first()->searchableUsing()->update($results, $this->name);
        }
    }

    public function estimate(): int
    {
        return 1;
    }

    public function title(): string
    {
        return 'Indexing...';
    }

    /**
     * @param  ImportSource  $source
     * @return Collection
     */
    public static function chunked(ImportSource $source, string $name): Collection
    {
        return $source->chunked()->map(function ($chunk) use ($name) {
            return new static($chunk, $name);
        });
    }
}
