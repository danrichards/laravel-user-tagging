<?php

namespace Dan\Tagging\Repositories\Taggable;

use Dan\Tagging\Repositories\AbstractCacheDecorator as BaseAbstractCacheDecorator;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Collection;

/**
 * Class CacheDecorator
 */
abstract class AbstractCacheDecorator extends BaseAbstractCacheDecorator implements TaggableInterface
{

    use TaggingUtility;

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
     * Perform the action of tagging the model with the given string
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     * @return mixed
     */
    public function tagForUser(Model $taggable, Model $user, $tagNames)
    {
        return $this->getCache('tagForUser', func_get_args(), function () use ($taggable, $user, $tagNames) {
            return $this->repo->tagForUser($taggable, $user, $tagNames);
        });
    }

    /**
     * Remove the tag from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param null $tagNames
     * @return mixed
     */
    public function untagForUser(Model $taggable, Model $user, $tagNames)
    {
        return $this->getCache('untagForUser', func_get_args(), function () use ($taggable, $user, $tagNames) {
            return $this->repo->untagForUser($taggable, $user, $tagNames);
        });
    }

    /**
     * Replace the tags from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     * @return mixed
     */
    public function retagForUser(Model $taggable, Model $user, $tagNames)
    {
        return $this->getCache('retagForUser', func_get_args(), function () use ($taggable, $user, $tagNames) {
            return $this->repo->retagForUser($taggable, $user, $tagNames);
        });
    }

    /**
     * Collection of TagsRepository, related to the current Model
     *
     * @param Model $taggable
     * @return \Illuminate\Database\Eloquent\Collection [TagsRepository]
     */
    public function tagsFor(Model $taggable)
    {
        return $this->getCache('tagsFor', func_get_args(), function () use ($taggable) {
            return $this->repo->tagsFor($taggable);
        });
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagNamesFor(Model $taggable)
    {
        return $this->getCache('tagNamesFor', func_get_args(), function () use ($taggable) {
            return $this->repo->tagNamesFor($taggable);
        });
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagSlugsFor(Model $taggable)
    {
        return $this->getCache('tagSlugsFor', func_get_args(), function () use ($taggable) {
            return $this->repo->tagSlugsFor($taggable);
        });
    }

    /**
     * Collection of Tagged for this Model
     *
     * @param Model $taggable
     * @return Collection [Tagged]
     */
    public function taggedFor(Model $taggable)
    {
        return $this->getCache('taggedFor', func_get_args(), function () use ($taggable) {
            return $this->repo->taggedFor($taggable);
        });
    }

    /**
     * Array of Tagged [__col__], related to the current Model
     *
     * @param Model $taggable
     * @param string $col
     * @return array [int]
     */
    public function taggedColFor(Model $taggable, $col = 'tag_slug')
    {
        return $this->getCache('taggedColFor', func_get_args(), function () use ($taggable, $col) {
            return $this->repo->taggedColFor($taggable);
        });
    }

    /**
     * Array of Tagged [id], related to the current Model
     *
     * @param Model $taggable
     * @return array [int]
     */
    public function taggedIdsFor(Model $taggable)
    {
        return $this->getCache('taggedIdsFor', func_get_args(), function () use ($taggable) {
            return $this->repo->taggedIdsFor($taggable);
        });
    }
    
    /**
     * @param Model $taggable
     * @param Model $user
     * @return Collection [Tagged]
     */
    public function taggedForUser(Model $taggable, Model $user)
    {
        return $this->getCache('taggedForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->taggedForUser($taggable, $user);
        });
    }

    /**
     * Array of Tagged [id] in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [int]
     */
    public function taggedIdsForUser(Model $taggable, Model $user)
    {
        return $this->getCache('taggedIdsForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->taggedIdsForUser($taggable, $user);
        });
    }

    /**
     * Get a column from Tagged
     *
     * @param Model $taggable
     * @param Model $user
     * @param string $col
     * @return array[int|string]
     */
    public function taggedColForUser(Model $taggable, Model $user, $col = 'tag_slug')
    {
        return $this->getCache('taggedColForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->taggedColForUser($taggable, $user);
        });
    }

    /**
     * Collection of Tag, which have been Tagged by User
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return Collection [Tagged]
     */
    public function tagsForUser(Model $taggable, Model $user)
    {
        return $this->getCache('tagsForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->tagsForUser($taggable, $user);
        });
    }

    /**
     * Array of Tagged [tag_slug] in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagSlugsForUser(Model $taggable, Model $user)
    {
        return $this->getCache('tagSlugsForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->tagSlugsForUser($taggable, $user);
        });
    }

    /**
     * Array of names in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagNamesForUser(Model $taggable, Model $user)
    {
        return $this->getCache('tagNamesForUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->tagNamesForUser($taggable, $user);
        });
    }

    /**
     * Array of TaggedUser [user_id], related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function userIdsWhoTagged(Model $taggable)
    {
        return $this->getCache('userIdsWhoTagged', func_get_args(), function () use ($taggable) {
            return $this->repo->userIdsWhoTagged($taggable);
        });
    }

    /**
     * Collection of User who tagged, related to the current model
     *
     * @param Model $taggable
     * @return Collection
     */
    public function usersWhoTagged(Model $taggable)
    {
        return $this->getCache('usersWhoTagged', func_get_args(), function () use ($taggable) {
            return $this->repo->usersWhoTagged($taggable);
        });
    }

    /**
     * Has the User tagged the current Model?
     *
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @return bool
     */
    public function isTaggedByUser(Model $taggable, $user)
    {
        return $this->getCache('isTaggedByUser', func_get_args(), function () use ($taggable, $user) {
            return $this->repo->isTaggedByUser($taggable, $user);
        });
    }

    /**
     * Has the User tagged the current Model with any of the $tags provided?
     *
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @param mixed $tags
     * @return bool
     */
    public function isTaggedByUserWith(Model $taggable, $user, $tags)
    {
        return $this->getCache('isTaggedByUserWith', func_get_args(), function () use ($taggable, $user, $tags) {
            return $this->repo->isTaggedByUserWith($taggable, $user, $tags);
        });
    }

}