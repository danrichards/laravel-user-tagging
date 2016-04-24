<?php

namespace Dan\Tagging\Repositories\Users;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\RepositoryInterface;

/**
 * Interface UsersInterface
 */
interface UsersInterface extends RepositoryInterface
{

    /**
     * @param \App\User|Model|int $user
     * @param string $taggedCol
     * @return array [int]
     */
    public function taggedColFor($user, $taggedCol = 'id');

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function tagsIdsFor($user);

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tag]
     */
    public function tagsFor($user);

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function taggedIdsFor($user);

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function taggedFor($user);

    /**
     * Models that have been tagged by the user.
     *
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggablesFor($user);

}