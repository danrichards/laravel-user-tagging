<?php

namespace Dan\Tagging\Repositories\Tagged;

use Dan\Tagging\Models\Tagged;
use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Torann\LaravelRepository\Repositories\AbstractRepository;

/**
 * Class TaggedRepository
 */
class TaggedRepository extends AbstractRepository implements TaggedInterface
{
    
    use TaggingUtility;

    /** @var string $model */
    protected $model = Tagged::class;

    /**
     * Find by taggable_type, taggable_id and slug.
     *
     * @param string $taggable_type
     * @param string $taggable_id
     * @param string $tag_slug
     * @return \Dan\Tagging\Models\Tagged|Model|null
     */
    public function findByModelKeySlug($taggable_type, $taggable_id, $tag_slug)
    {
        /** @var  $util \Dan\Tagging\Contracts\TaggingUtility */
        $util = app(\Dan\Tagging\Contracts\TaggingUtility::class);
        $tag = current($util->slugArray($tag_slug));
        return $util->taggedModel()
            ->where('tag_slug', $tag)
            ->where('taggable_type', $taggable_type)
            ->where('taggable_id', $taggable_id)
            ->get()
            ->first();
    }

    /**
     * Find a Tagged model provided a Tag and Taggable
     *
     * @param \Illuminate\Database\Eloquent\Model $taggable
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tagged|Model|null
     */
    public function findByTaggableTag(Model $taggable, Model $tag)
    {
        return $this->findByModelKeySlug(
            get_class($taggable),
            $taggable->getKey(),
            $tag->slug
        );
    }

    /**
     * Find by Tag and Taggable or create a new Tagged model.
     *
     * @param \Illuminate\Database\Eloquent\Model $taggable
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @param bool &$tagWasTagged 	Was the Tag was just Tagged?
     * @return \Dan\Tagging\Models\Tagged|Model
     */
    public function findByTaggableTagOrCreate(Model $taggable, Model $tag, &$tagWasTagged = null)
    {
        $tagWasTagged = empty($tagged = static::findByTaggableTag($taggable, $tag));
        if (! $tagWasTagged) {
            return $tagged;
        }

        /** @var \Dan\Tagging\Contracts\TaggingUtility $util */
        $util = app(\Dan\Tagging\Contracts\TaggingUtility::class);
        $tagged = $util->taggedModel([
            'taggable_id' => $taggable->getKey(),
            'taggable_type' => get_class($taggable),
            'tag_name' => $tag->name,
            'tag_slug' => $tag->slug,
            'users_count' => '0'
        ]);

        $tagged->save();

        return $tagged;
    }

    /**
     * Related Tag for the Tagged model
     *
     * @param Model $tagged
     * @return \Dan\Tagging\Models\Tag|Model
     */
    public function tagFor(Model $tagged)
    {
        /** @var \Dan\Tagging\Repositories\Tags\TagsInterface $tags */
        $tags = app($this->tagUtil()->tagsRepositoryInterface());
        return $tags->findTag($tagged->tag_slug);
    }

    /**
     * Related Taggable for the Tagged model
     *
     * @param Model $tagged
     * @return Model
     */
    public function taggableFor(Model $tagged)
    {
        $taggables = config('tagging.taggable_interfaces');
        foreach ($taggables as $model => $interface) {
            if (ltrim($model, '\\') == ltrim($tagged->taggable_type, '\\')) {
                $taggableRepository = app($interface);
                return $taggableRepository->find($tagged->taggable_id);
            }
        }
        return null;
    }

    /**
     * Array of users ids who have tagged this model (with a certain tag)
     *
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     * @return array [int]
     */
    public function usersIdsFor(Model $tagged)
    {
        $taggedUser = $this->tagUtil()->taggedUserModelString();
        $userIds = $taggedUser::select('user_id')
            ->where('tagged_id', $tagged->getKey())
            ->pluck('user_id')
            ->all();
        return $userIds;
    }

    /**
     * Users who have tagged this model.
     *
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     * @return \Illuminate\Database\Eloquent\Collection [\Dan\Tagging\Repositories\UsersRepository]
     */
    public function usersFor(Model $tagged)
    {
        /** @var \Dan\Tagging\Repositories\Users\UsersRepository $users */
        $users = app($this->tagUtil()->usersRepositoryInterface());
        return $users->findAllBy('id', $this->usersIdsFor($tagged));
    }

    /**
     * Update the user count on the Tagged model.
     *
     * @param \Dan\Tagging\Models\Tagged|Model $tagged
     */
    public function recalculateFor(Model $tagged)
    {
        $tagged->recalculate();
        // TODO: clear any caches necessary
    }

}