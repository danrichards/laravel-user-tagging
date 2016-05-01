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
     * @return array[int]
     */
    public function tagsIdsFor($user);

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tag]
     */
    public function tagsFor($user);

    /**
     * Collection of tags with counts for user.
     *
     * @param \App\User|\Illuminate\Database\Eloquent\Model|int $user
     * @param string $order
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsForUserWithCounts($user, $order = 'count', $sort = 'ASC');

    /**
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function taggedFor($user);

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
    public function taggedIdsFor($user);

    /**
     * Models that have been tagged by the user.
     *
     * @param \App\User|Model|int $user
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggablesFor($user);

}