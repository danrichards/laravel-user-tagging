<?php

namespace Dan\Tagging\Testing\Integration\Setup\Users;

use Torann\LaravelRepository\Repositories\AbstractCacheDecorator;

/**
 * Class CacheDecorator
 *
 * @package App\Gistribute\Repositories\Users
 */
class CacheDecorator extends AbstractCacheDecorator implements UsersInterface
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