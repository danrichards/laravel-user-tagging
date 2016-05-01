<?php

namespace Dan\Tagging\Traits;

use Dan\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\RepositoryFactory;

/**
 * Copyright (C) 2014 Robert Conner
 *
 * @self \Illuminate\Database\Eloquent\Model
 */
trait Taggable {

    /**
     * @var \Dan\Tagging\Util $util
     */
    protected $taggingUtility;

    /**
     * @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository
     */
    protected $repository;

    /**
     * @return TaggingUtility
     */
    public function tagUtil()
    {
        return is_null($this->taggingUtility)
            ? $this->taggingUtility = app(TaggingUtility::class)
            : $this->taggingUtility;
    }

    /**
     * @return mixed
     */
    public function tagged()
    {
        return $this->morphMany($this->tagUtil()->taggedModelString(), 'taggable');
    }

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model $user
     * @param mixed $tagNames
     */
    public function tagForUser(Model $user, $tagNames)
    {
        $this->getRepository()->tagForUser($this, $user, $tagNames);
    }

    /**
     * Remove the tag from this model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model $user
     * @param mixed $tagNames
     */
    public function untagForUser(Model $user, $tagNames)
    {
        $this->getRepository()->untagForUser($this, $user, $tagNames);
    }

    /**
     * Replace the tags from this model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model $user
     * @param $tagNames
     */
    public function retagForUser(Model $user, $tagNames)
    {
        $this->getRepository()->retagForUser($this, $user, $tagNames);
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @return array
     */
    public function tagNames()
    {
        return $this->getRepository()->taggedColFor($this, 'tag_name');
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @return array
     */
    public function tagSlugs()
    {
        return $this->getRepository()->taggedColFor($this, 'tag_slug');
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @return array
     */
    public function tagNamesForUser($user)
    {
        return $this->getRepository()->taggedColForUser($this, $user, 'tag_slug');
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @return array
     */
    public function tagSlugsForUser($user)
    {
        return $this->getRepository()->tagSlugsForUser($this, $user);
    }

    /**
     * Get a repository from a Model
     *
     * @param bool $withCache
     * @return \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository
     * @throws \Exception
     */
    public function getRepository($withCache = true)
    {
        if (is_null($this->repository)) {

            $namespace = explode('\\', __CLASS__);
            $name = str_plural(array_pop($namespace));
            array_pop($namespace); // lose the 'Models'
            array_push($namespace, 'Repositories');
            $namespace = implode('\\', $namespace);

            $withCache = is_null($withCache)
                ? config('repositories.cache.enabled', false) : $withCache;

            return $withCache
                ? $this->repository = RepositoryFactory::createWithCache($name, $namespace)
                : $this->repository = RepositoryFactory::create($name, $namespace);
        } else {
            return $this->repository;
        }
    }

}
