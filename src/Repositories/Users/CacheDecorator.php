<?php

namespace Dan\Tagging\Repositories\Users;

use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Repositories\AbstractCacheDecorator;

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
        return $this->getCache('tagsIdsFor', func_get_args(), function () use ($user) {
            return $this->repo->tagsIdsFor($user);
        });
    }

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tag]
     */
    public function tagsFor($user)
    {
        dd(__CLASS__);
        return $this->getCache('tagsFor', func_get_args(), function () use ($user) {
            return $this->repo->tagsFor($user);
        });
    }

    /**
     * Collection of tags with counts for user.
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @param string $order
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsForUserWithCounts($user, $order = 'my_count', $sort = 'ASC')
    {
        return $this->getCache('tagsForUserWithCounts', func_get_args(), function () use ($user) {
            return $this->repo->tagsForUserWithCounts($user);
        });
    }

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function taggedIdsFor($user)
    {
        return $this->getCache('taggedIdsFor', func_get_args(), function () use ($user) {
            return $this->repo->taggedIdsFor($user);
        });
    }

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function taggedFor($user)
    {
        return $this->getCache('taggedFor', func_get_args(), function () use ($user) {
            return $this->repo->taggedFor($user);
        });
    }

    /**
     * @param \App\User|Model|int $user
     * @param string $taggedCol
     * @return array [int]
     */
    public function taggedColFor($user, $taggedCol = 'id')
    {
        return $this->getCache('taggedFor', func_get_args(), function () use ($user) {
            return $this->repo->taggedFor($user);
        });
    }

    /**
     * Models that have been tagged by the user.
     *
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggablesFor($user)
    {
        return $this->getCache('taggedTaggablesFor', func_get_args(), function () use ($user) {
            return $this->repo->taggedTaggablesFor($user);
        });
    }
}