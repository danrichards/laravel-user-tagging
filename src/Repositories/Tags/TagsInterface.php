<?php

namespace Dan\Tagging\Repositories\Tags;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\RepositoryInterface;

/**
 * Interface TagsInterface
 */
interface TagsInterface extends RepositoryInterface
{

    /**
     * Collection of Users Repositories who have tagged something with this tag.
     *
     * @param \Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model $tag
     * @return \Illuminate\Database\Eloquent\Collection [\App\User|Model]
     */
    public function usersFor($tag);

    /**
     * Tag Repository by slug.
     *
     * @param mixed $tag
     * @return \Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model|null
     */
    public function findTag($tag);

    /**
     * Collection of Tag Repositories by an array of slugs.
     *
     * @param mixed $tags
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model]
     */
    public function findTags($tags);

    /**
     * Get a Tag Repository or create a new one if it does not exist.
     *
     * @param string $name
     * @param bool &$tagWasCreated 		Was the Tag was just created?
     * @return \Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model|null
     */
    public function findOrCreate($name, &$tagWasCreated = null);

    /**
     * Update the count on the Tag model.
     *
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tag|Model $tag
     */
    public function recalculate(Model $tag);
    
}