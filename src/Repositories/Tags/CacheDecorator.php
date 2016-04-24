<?php

namespace Dan\Tagging\Repositories\Tags;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\AbstractCacheDecorator;

/**
 * Class CacheDecorator
 */
class CacheDecorator extends AbstractCacheDecorator implements TagsInterface
{

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
     * Collection of Users Repositories who have tagged something with this tag.
     *
     * @param string|\Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model $tag
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Repositories\Users\UsersInterface]
     */
    public function usersFor($tag)
    {
        return (new TagsRepository())->usersFor($tag);
    }

    /**
     * Tag Repository by slug.
     *
     * @param mixed $tag
     * @return \Dan\Tagging\Repositories\Tags\TagsInterface|null
     */
    public function findTag($tag)
    {
        return (new TagsRepository())->findTags($tag);
    }

    /**
     * Collection of Tag Repositories by an array of slugs.
     *
     * @param mixed $tags
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Repositories\Tags\TagsInterface]
     */
    public function findTags($tags)
    {
        return (new TagsRepository())->findTags($tags);
    }

    /**
     * Get a Tag Repository or create a new one if it does not exist.
     *
     * @param string $name
     * @param bool &$tagWasCreated Was the Tag was just created?
     * @return \Dan\Tagging\Repositories\Tags\TagsRepository
     */
    public function findOrCreate($name, &$tagWasCreated = null)
    {
        return (new TagsRepository())->findOrCreate($name, $tagWasCreated = null);
    }

    /**
     * Update the count on the Tag model.
     *
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tag|Model $tag
     */
    public function recalculate(Model $tag)
    {
        return (new TagsRepository())->recalculate($tag);
    }
}