<?php

namespace Dan\Tagging\Repositories\Tags;

use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Repositories\AbstractCacheDecorator;

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
        return $this->getCache('usersFor', func_get_args(), function () use ($tag) {
            return $this->repo->usersFor($tag);
        });
    }

    /**
     * Tag Repository by slug.
     *
     * @param mixed $tag
     * @return \Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model|null
     */
    public function findTag($tag)
    {
        return $this->getCache('findTag', func_get_args(), function () use ($tag) {
            return $this->repo->findTag($tag);
        });
    }

    /**
     * Collection of Tag Repositories by an array of slugs.
     *
     * @param mixed $tags
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Repositories\Tags\TagsInterface]
     */
    public function findTags($tags)
    {
        return $this->getCache('findTags', func_get_args(), function () use ($tags) {
            return $this->repo->findTags($tags);
        });
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
        return $this->getCache('findOrCreate', func_get_args(), function () use ($name, &$tagWasCreated) {
            return $this->repo->findOrCreate($name, $tagWasCreated);
        });
    }

    /**
     * Update the count on the Tag model.
     *
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tag|Model $tag
     */
    public function recalculateFor(Model $tag)
    {
        return $this->repo->recalculate($tag);
    }
}