<?php

namespace Matchish\ScoutElasticSearch\Engines\Helpers;

use Illuminate\Support\Arr;
use Matchish\ScoutElasticSearch\Creator\ProxyClient;

class Index {

    public static function getName(string $alias, bool $isIndexing): string
    {
        if($isIndexing) {

            return self::getLatestIndex($alias) ?? $alias;
        }

        return $alias;
    }

    public static function getList(string $alias): array
    {
        $indices = [];
        $client = app(ProxyClient::class);

        foreach (array_keys($client->indices()->stats()['indices']) as $name) {
            $index = [];
            $valid = preg_match(sprintf('/^(%s)_(\d{10})$/', $alias), $name, $index);

            if((int) $valid > 0) {
                list($indexName, $aliasName, $timestamp) = $index;
                $indices[(int) $timestamp] = $indexName;
            }
        }

        ksort($indices, SORT_NUMERIC);

        return array_values($indices);
    }

    private static function getLatestIndex(string $alias): ?string
    {
        return Arr::last(self::getList($alias), fn () => true, null);
    }
}
