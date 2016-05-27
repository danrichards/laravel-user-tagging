<?php

namespace Dan\Tagging\Repositories\Taggable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Torann\LaravelRepository\Repositories\RepositoryInterface;

/**
 * Interface AbstractInterface
 */
interface TaggableInterface extends RepositoryInterface
{

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     */
    public function tagForUser(Model $taggable, Model $user, $tagNames);

    /**
     * Remove the tag from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param null $tagNames
     */
    public function untagForUser(Model $taggable, Model $user, $tagNames);

    /**
     * Replace the tags from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     * @internal param string $tagName or array
     */
    public function retagForUser(Model $taggable, Model $user, $tagNames);

    /**
     * Collection of TagsRepository, related to the current Model
     *
     * @param Model $taggable
     * @return Collection [TagsRepository]
     */
    public function tagsFor(Model $taggable);

    /**
     * Array of the tag names related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagNamesFor(Model $taggable);

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagSlugsFor(Model $taggable);

    /**
     * Collection of Tagged for this Model
     *
     * @param Model $taggable
     * @return Collection [Tagged]
     */
    public function taggedFor(Model $taggable);

    /**
     * Array of Tagged [__col__], related to the current Model
     *
     * @param Model $taggable
     * @param string $col
     * @return array [int]
     */
    public function taggedColFor(Model $taggable, $col = 'tag_slug');

    /**
     * Array of Tagged [id], related to the current Model
     *
     * @param Model $taggable
     * @return array [int]
     */
    public function taggedIdsFor(Model $taggable);

    /**
     * @param Model $taggable
     * @param Model $user
     * @return Collection [Tagged]
     */
    public function taggedForUser(Model $taggable, Model $user);

    /**
     * Array of Tagged [ids] in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [int]
     */
    public function taggedIdsForUser(Model $taggable, Model $user);
    
    /**
     * Get a column from Tagged
     *
     * @param Model $taggable
     * @param Model $user
     * @param string $col
     * @return array[int|string]
     */
    public function taggedColForUser(Model $taggable, Model $user, $col = 'tag_slug');

    /**
     * Collection of Tag, which have been Tagged by User
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return Collection [Tagged]
     */
    public function tagsForUser(Model $taggable, Model $user);

    /**
     * Array of slugs in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagSlugsForUser(Model $taggable, Model $user);

    /**
     * Array of names in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagNamesForUser(Model $taggable, Model $user);

    /**
     * Array of user ids related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function userIdsWhoTagged(Model $taggable);

    /**
     * Collection of User who tagged, related to the current model
     *
     * @param Model $taggable
     * @return Collection
     */
    public function usersWhoTagged(Model $taggable);

    /**
     * Has the User tagged the current Model?
     *
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @return bool
     */
    public function isTaggedByUser(Model $taggable, $user);

    /**
     * Has the User tagged the current Model with any of the $tags provided?
     *
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @param mixed $tags
     * @return bool
     */
    public function isTaggedByUserWith(Model $taggable, $user, $tags);

}