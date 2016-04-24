<?php

namespace Dan\Tagging\Traits;

use Dan\Tagging\Contracts\TaggingUtility;

/**
 * Trait TaggableUser
 *
 * @self \App\User
 */
trait TaggableUser {

    /**
     * @return array[int]
     */
    public function tagsIds()
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersInterface $usersRepository */
        $usersRepository = app(config('tagging.users_interface', '\Dan\Tagging\Repositories\Users\UsersInterface'));
        /** @var \App\User $this */
        return $usersRepository->tagsIdsFor($this);
    }

    /**
     * Tags for the current user Model
     */
    public function tags()
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersInterface $usersRepository */
        $usersRepository = app(config('tagging.users_interface', '\Dan\Tagging\Repositories\Users\UsersInterface'));
        /** @var \App\User $this */
        return $usersRepository->tagsFor($this);
    }

    /**
     * @return array[int]
     */
    public function taggedIds()
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersInterface $usersRepository */
        $usersRepository = app(config('tagging.users_interface', '\Dan\Tagging\Repositories\Users\UsersInterface'));
        /** @var \App\User $this */
        return $usersRepository->taggedIdsFor($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function tagged()
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersInterface $usersRepository */
        $usersRepository = app(config('tagging.users_interface', '\Dan\Tagging\Repositories\Users\UsersInterface'));
        /** @var \App\User $this */
        return $usersRepository->taggedFor($this);
    }

    /**
     * Models that have been tagged by the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggables()
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersInterface $usersRepository */
        $usersRepository = app(config('tagging.users_interface', '\Dan\Tagging\Repositories\Users\UsersInterface'));
        /** @var \App\User $this */
        return $usersRepository->taggedTaggablesFor($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taggedBelongsToMany()
    {
        /** @var TaggingUtility $util */
        $util = app(TaggingUtility::class);
        return $this->belongsToMany(
            $util->taggedModelString(),
            'tagging_tagged_user',
            'user_id',
            'tagged_id'
        );
    }

}
