<?php

namespace Dan\Tagging;

use App\User;
use Dan\Tagging\Contracts\TaggingUtility;

/**
 * Copyright (C) 2014 Robert Conner
 *
 * @self \Illuminate\Database\Eloquent\Model
 */
trait Taggable {

    /**
     * @var TaggingUtility $util
     */
    protected $taggingUtility;

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
        return $this->morphMany('Dan\Tagging\Models\Tagged', 'taggable');
    }

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param User $user
     * @param mixed $tagNames
     */
    public function tagForUser(User $user, $tagNames)
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        $tags->tagForUser($this, $user, $tagNames);
    }

    /**
     * Remove the tag from this model
     *
     * @param User $user
     * @param mixed $tagNames
     */
    public function untagForUser(User $user, $tagNames)
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        $tags->untagForUser($this, $user, $tagNames);
    }

    /**
     * Replace the tags from this model
     *
     * @param User $user
     * @param $tagNames
     */
    public function retagForUser(User $user, $tagNames)
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        $tags->retagForUser($this, $user, $tagNames);
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @return array
     */
    public function tagNames()
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        return $tags->taggedColFor($this, 'tag_name');
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @return array
     */
    public function tagSlugs()
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        return $tags->taggedColFor($this, 'tag_slug');
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @return array
     */
    public function tagNamesForUser($user)
    {
        /** @var \Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository $tags */
        $tags = app($this->tagUtil()->taggableRepositoryInterface(__CLASS__));
        /** @var \Illuminate\Database\Eloquent\Model $this */
        return $tags->taggedColForUser($this, 'tag_slug');
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @return array
     */
    public function tagSlugsForUser($user)
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersRepository $tags */
        $users = app($this->tagUtil()->usersRepositoryInterface());
        return $users->taggedColFor($user, 'tag_name');
    }

}
