<?php

namespace Dan\Tagging\Repositories\Tags;

use Dan\Tagging\Collection;
use Dan\Tagging\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Torann\LaravelRepository\Repositories\AbstractRepository;

/**
 * Class TagsRepository
 */
class TagsRepository extends AbstractRepository implements TagsInterface
{

    use TaggingUtility;

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Tag::class;

    /**
     * Users who have tagged something with this tag.
     *
     * @param string|\Dan\Tagging\Models\Tag|\Illuminate\Database\Eloquent\Model $tag
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function usersFor($tag)
    {
        $slug = $tag instanceof Model
            ? $tag->slug : $this->tagUtil()->slug($tag);

        // ids of the Tagged
        $tagged = $this->tagUtil()->taggedModelString();
        $taggedIds = $tagged::where('tag_slug', $slug)
            ->pluck('id')->all();

        if (empty($taggedIds)) {
            return new Collection();
        }

        // user ids of TaggedUser
        $taggedUser = $this->tagUtil()->taggedUserModelString();
        $usersIds = $taggedUser::whereIn('tagged_id', $taggedIds)
            ->pluck('user_id')->all();
        $usersIds = array_unique($usersIds);

        /** @var \App\User $user */
        $user = $this->tagUtil()->userModelString();
        return $user::findMany($usersIds);
    }

    /**
     * Get a Tag model by slug.
     *
     * @param mixed $tag
     * @return Tag|null
     */
    public function findTag($tag)
    {
        $tag = current($this->tagUtil()->slugArray($tag));
        $tagModel = $this->tagUtil()->tagModelString();
        return $tagModel::where('slug', $tag)->first();
    }

    /**
     * Get many Tag models by an array of slugs.
     *
     * @param mixed $tags
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findTags($tags)
    {
        $tags = $this->tagUtil()->slugArray($tags);
        $tagModel = $this->tagUtil()->tagModelString();
        return $tagModel::whereIn('slug', $tags)->get();
    }

    /**
     * Get a Tag model or create a new one if it does not exist.
     *
     * @param string $name
     * @param bool &$tagWasCreated 		Was the Tag was just created?
     * @return Tag|null
     */
    public function findOrCreate($name, &$tagWasCreated = null)
    {
        $tagWasCreated = empty($tag = self::findTag($name));
        if (! $tagWasCreated) {
            return $tag;
        }
        /** @var \Dan\Tagging\Contracts\TaggingUtility $util */
        $slug = $this->tagUtil()->slug($name);
        $count = 0;
        $tag = $this->tagUtil()->tagModel(compact('name', 'slug', 'count'));
        $tag->save();
        return $tag;
    }

    /**
     * Update the count on the Tag model.
     *
     * @param \Dan\Tagging\Models\Tag|Model $tag
     * @return \Dan\Tagging\Models\Tag|Model $tag
     */
    public function recalculateFor(Model $tag)
    {
        return $tag->recalculate();
    }
    
}