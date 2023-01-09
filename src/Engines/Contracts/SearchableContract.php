<?php

namespace Matchish\ScoutElasticSearch\Engines\Contracts;

interface SearchableContract
{
    public function searchableAs(bool $isIndexing = false): string;
    public function toSearchableArray(): array;
}
