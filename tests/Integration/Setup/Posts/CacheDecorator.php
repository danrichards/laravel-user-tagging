<?php

namespace Dan\Tagging\Testing\Integration\Setup\Posts;

use Dan\Tagging\Repositories\Taggable\AbstractCacheDecorator;

/**
 * Class CacheDecorator
 */
class CacheDecorator extends AbstractCacheDecorator implements PostsInterface
{

    /**
     * Lifetime of the cache.
     *
     * @var int
     */
    protected $cacheMinutes = 60;

    /**
     * @var array $skipCache
     */
    protected $skipCache = [];

}