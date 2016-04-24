<?php

namespace Dan\Tagging\Repositories\Taggable;

use Torann\LaravelRepository\Repositories\AbstractCacheDecorator as BaseAbstractCacheDecorator;
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
     */
    public function tagForUser(Model $taggable, Model $user, $tagNames)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        $repo->tagForUser($taggable, $user, $tagNames);
    }

    /**
     * Remove the tag from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param null $tagNames
     */
    public function untagForUser(Model $taggable, Model $user, $tagNames)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        $repo->untagForUser($taggable, $user, $tagNames);
    }

    /**
     * Replace the tags from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     */
    public function retagForUser(Model $taggable, Model $user, $tagNames)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        $repo->retagForUser($taggable, $user, $tagNames);
    }

    /**
     * Collection of TagsRepository, related to the current Model
     *
     * @param Model $taggable
     * @return \Illuminate\Database\Eloquent\Collection [TagsRepository]
     */
    public function tagsFor(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagsFor($taggable);
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagNamesFor(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagNamesFor($taggable);
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagSlugsFor(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagSlugsFor($taggable);
    }

    /**
     * Collection of Tagged for this Model
     *
     * @param Model $taggable
     * @return Collection [Tagged]
     */
    public function taggedFor(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedFor($taggable);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedColFor($taggable, $col);
    }

    /**
     * Array of Tagged [id], related to the current Model
     *
     * @param Model $taggable
     * @return array [int]
     */
    public function taggedIdsFor(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedIdsFor($taggable);
    }

    /**
     * @param Model $taggable
     * @param Model $user
     * @return Collection [Tagged]
     */
    public function taggedForUser(Model $taggable, Model $user)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedForUser($taggable, $user);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedIdsForUser($taggable, $user);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->taggedColForUser($taggable, $user, $col);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagsForUser($taggable, $user);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagSlugsForUser($taggable, $user);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->tagNamesForUser($taggable, $user);
    }

    /**
     * Array of TaggedUser [user_id], related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function userIdsWhoTagged(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->userIdsWhoTagged($taggable);
    }

    /**
     * Collection of User who tagged, related to the current model
     *
     * @param Model $taggable
     * @return Collection
     */
    public function usersWhoTagged(Model $taggable)
    {
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->usersWhoTagged($taggable);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->isTaggedByUser($taggable, $user);
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
        /** @var AbstractTaggableRepository $repo */
        $repo = app($this->tagUtil()->taggableRepositoryInterface($this->getModel()));
        return $repo->isTaggedByUserWith($taggable, $user, $tags);
    }

}