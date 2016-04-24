<?php

namespace Dan\Tagging\Repositories\Tagged;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\RepositoryInterface;

/**
 * Interface TaggedInterface
 */
interface TaggedInterface extends RepositoryInterface
{
    
    /**
     * Find by taggable_type, taggable_id and slug.
     *
     * @param string $taggable_type
     * @param string $taggable_id
     * @param string $tag_slug
     * @return \Dan\Tagging\Models\Tagged|Model|null
     */
    public static function findByModelKeySlug($taggable_type, $taggable_id, $tag_slug);

    /**
     * Find a Tagged model provided a Tag and Taggable
     *
     * @param \Illuminate\Database\Eloquent\Model $taggable
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tagged|Model|null
     */
    public static function findByTaggableTag(Model $taggable, Model $tag);

    /**
     * Find by Tag and Taggable or create a new Tagged model.
     *
     * @param \Illuminate\Database\Eloquent\Model $taggable
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @param bool &$tagWasTagged 	Was the Tag was just Tagged?
     * @return \Dan\Tagging\Models\Tagged|Model
     */
    public static function findByTaggableTagOrCreate(Model $taggable, Model $tag, &$tagWasTagged);

    /**
     * Related Tag for the Tagged model
     * 
     * @param Model $tagged
     * @return \Dan\Tagging\Models\Tag|Model
     */
    public function tagFor(Model $tagged);

    /**
     * Related Taggable for the Tagged model
     * 
     * @param Model $tagged
     * @return Model
     */
    public function taggableFor(Model $tagged);
    
    /**
     * Array of users ids who have tagged this model (with a certain tag)
     *
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     * @return array [int]
     */
    public function usersIdsFor(Model $tagged);

    /**
     * Users which are associated to this Tagged instance through TaggedUser
     *
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Repositories\Users\UsersInterface]
     */
    public function usersFor(Model $tagged);

    /**
     * Update the count on the Tag model.
     * 
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     */
    public function recalculateFor(Model $tagged);

}