<?php

namespace Matchish\ScoutElasticSearch\Jobs;

use Illuminate\Support\Collection;
use Matchish\ScoutElasticSearch\ElasticSearch\Index;
use Matchish\ScoutElasticSearch\Jobs\Stages\CleanUp;
use Matchish\ScoutElasticSearch\Jobs\Stages\CreateWriteIndex;
use Matchish\ScoutElasticSearch\Jobs\Stages\PullFromSource;
use Matchish\ScoutElasticSearch\Jobs\Stages\RefreshIndex;
use Matchish\ScoutElasticSearch\Jobs\Stages\SwitchToNewAndRemoveOldIndex;
use Matchish\ScoutElasticSearch\Searchable\ImportSource;

class ImportStages extends Collection
{
    /**
     * @param  ImportSource  $source
     * @return Collection
     */
    public static function fromSource(ImportSource $source)
    {
        $index = Index::fromSource($source);
        $indexName = $index->name();

        return (new self([
            new CleanUp($source),
            new CreateWriteIndex($source, $index, $indexName),
            PullFromSource::chunked($source, $indexName),
            new RefreshIndex($index),
            new SwitchToNewAndRemoveOldIndex($source, $index, $indexName),
        ]))->flatten()->filter();
    }
}
