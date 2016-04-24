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
        
    }
    
    public function taggableFor(Model $tagged)
    {
        
    }

    public function usersIdsFor(Model $tagged)
    {
        return (new TaggedRepository())->userIdsFor($tagged);
    }

    public function usersFor(Model $tagged)
    {
        return (new TaggedRepository())->usersFor($tagged);
    }

    public function recalculateFor(Model $tagged)
    {
        return (new TaggedRepository())->recalculateFor($tagged);
    }

    public static function findByModelKeySlug($taggable_type, $taggable_id, $tag_slug)
    {
        return TaggedRepository::findByModelKeySlug($taggable_type, $taggable_id, $tag_slug);
    }

    public static function findByTaggableTag(Model $taggable, Model $tag)
    {
        return TaggedRepository::findByTaggableTag($taggable, $tag);
    }

    public static function findByTaggableTagOrCreate(Model $taggable, Model $tag, &$tagWasTagged)
    {
        return TaggedRepository::findByTaggableTagOrCreate($taggable, $tag, $tagWasTagged);
    }
}