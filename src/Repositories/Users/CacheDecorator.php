<?php

namespace Dan\Tagging\Repositories\Users;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\AbstractCacheDecorator;

/**
 * Class CacheDecorator
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

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function tagsIdsFor($user)
    {
        return (new UsersRepository())->tagsIdsFor($user);
    }

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tag]
     */
    public function tagsFor($user)
    {
        return (new UsersRepository())->tagsFor($user);
    }

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function taggedIdsFor($user)
    {
        return (new UsersRepository())->taggedIdsFor($user);
    }

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function taggedFor($user)
    {
        return (new UsersRepository())->taggedFor($user);
    }

    /**
     * Models that have been tagged by the user.
     *
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggablesFor($user)
    {
        return (new UsersRepository())->taggedTaggablesFor($user);
    }
}