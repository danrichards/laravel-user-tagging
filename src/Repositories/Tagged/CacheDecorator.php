<?php

namespace Dan\Tagging\Repositories\Tagged;

use Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository;
use Dan\Tagging\Repositories\Tags\TagsInterface;
use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\AbstractCacheDecorator;

/**
 * Class CacheDecorator
 */
class CacheDecorator extends AbstractCacheDecorator implements TaggedInterface
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

    public function tagFor(Model $tagged)
    {
        return $this->getCache('tagFor', func_get_args(), function () use ($tagged) {
            return $this->repo->tagFor($tagged);
        });
    }
    
    public function taggableFor(Model $tagged)
    {
        return $this->getCache('taggableFor', func_get_args(), function () use ($tagged) {
            return $this->repo->taggableFor($tagged);
        });
    }

    public function usersIdsFor(Model $tagged)
    {
        return $this->getCache('usersIdsFor', func_get_args(), function () use ($tagged) {
            return $this->repo->usersIdsFor($tagged);
        });
    }

    public function usersFor(Model $tagged)
    {
        return $this->getCache('usersFor', func_get_args(), function () use ($tagged) {
            return $this->repo->usersFor($tagged);
        });
    }

    public function recalculateFor(Model $tagged)
    {
        return $this->repo->recalculateFor($tagged);
    }

    public function findByModelKeySlug($taggable_type, $taggable_id, $tag_slug)
    {
        return $this->getCache('findByModelKeySlug', func_get_args(), function () use ($taggable_type, $taggable_id, $tag_slug) {
            return $this->repo->findByModelKeySlug($taggable_type, $taggable_id, $tag_slug);
        });
    }

    public function findByTaggableTag(Model $taggable, Model $tag)
    {
        return $this->getCache('findByTaggableTag', func_get_args(), function () use ($taggable, $tag) {
            return $this->repo->findByTaggableTag($taggable, $tag);
        });
    }

    public function findByTaggableTagOrCreate(Model $taggable, Model $tag, &$tagWasTagged = null)
    {
        return $this->getCache('findByTaggableTagOrCreate', func_get_args(), function () use ($taggable, $tag, &$tagWasTagged) {
            return $this->repo->findByTaggableTagOrCreate($taggable, $tag, $tagWasTagged);
        });
    }
}