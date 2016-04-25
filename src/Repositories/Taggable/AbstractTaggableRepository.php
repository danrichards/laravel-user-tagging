<?php

namespace Dan\Tagging\Repositories\Taggable;

use Torann\LaravelRepository\Repositories\AbstractRepository;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Events\TaggedByUser;
use Dan\Tagging\Collection;

/**
 * Class AbstractTaggableRepository // build test
 */
abstract class AbstractTaggableRepository extends AbstractRepository implements TaggableInterface
{

    use TaggingUtility;

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     */
    public function tagForUser(Model $taggable, Model $user, $tagNames)
    {
        $tagNames = $this->tagUtil()->titleArray($tagNames);

        foreach($tagNames as $tagName) {
            $this->addTagForUser($taggable, $user, $tagName);
        }

        $slugsAdded = $this->tagUtil()->slugArray($tagNames);
        $slugsRemoved = [];
        event(new TaggedByUser($taggable, $user, $slugsAdded, $slugsRemoved));
    }

    /**
     * Remove the tag from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param null $tagNames
     */
    public function untagForUser(Model $taggable, Model $user, $tagNames)
    {
        $tagNames = $this->tagUtil()->titleArray($tagNames);

        foreach ($tagNames as $tagName) {
            $this->removeTagForUser($taggable, $user, $tagName);
        }

        $slugsAdded = [];
        $slugsRemoved = $this->tagUtil()->slugArray($tagNames);
        event(new TaggedByUser($taggable, $user, $slugsAdded, $slugsRemoved));
    }

    /**
     * Replace the tags from this model
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagNames
     */
    public function retagForUser(Model $taggable, Model $user, $tagNames)
    {
        $tagNames = $this->tagUtil()->titleArray($tagNames);
        $currentTagNames = $this->tagsForUser($taggable, $user)->pluck('name')->all();
        $deletions = array_diff($currentTagNames, $tagNames);
        $slugsRemoved = $this->tagUtil()->slugArray($deletions);
        foreach ($slugsRemoved as $slug) {
            $this->removeTagForUser($taggable, $user, $slug);
        }

        $additions = array_diff($tagNames, $currentTagNames);
        $slugsAdded = $this->tagUtil()->slugArray($additions);

        foreach ($additions as $tagName) {
            $this->addTagForUser($taggable, $user, $tagName);
        }

        event(new TaggedByUser($taggable, $user, $slugsAdded, $slugsRemoved));
    }

    /**
     * Adds a single tag
     *
     * @param Model $taggable
     * @param Model $user
     * @param $tagName string
     */
    private function addTagForUser(Model $taggable, Model $user, $tagName)
    {
        $tagWasCreated = true;		// set by reference
        /** @var \Dan\Tagging\Repositories\Tags\TagsRepository $tags */
        $tags = app($this->tagUtil()->tagsRepositoryInterface());
        $tag = $tags->findOrCreate($tagName, $tagWasCreated);

        if (! $tagWasCreated && $this->isTaggedByUserWith($taggable, $user, $tag->slug)) {
            return;		            // Nothing to do here.
        }

        $tagWasTagged = true;		// set by reference
        /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $tagged */
        $tagged = app($this->tagUtil()->taggedRepositoryInterface());
        $tagged = $tagged->findByTaggableTagOrCreate($taggable, $tag, $tagWasTagged);

        if ($tagWasCreated || $tagWasTagged) {
            $tag->recalculate();
        }

        /** @var \Dan\Tagging\Models\Tagged $tagged */
        $taggedUser = $this->tagUtil()->taggedUserModel([
            'tagged_id' => $tagged->id,
            'user_id' => $user->id
        ]);
        $taggedUser->save();

        $tagged->recalculate();
    }

    /**
     * Removes a single tag
     *
     * @param Model $taggable
     * @param Model $user
     * @param string $slug
     */
    private function removeTagForUser(Model $taggable, Model $user, $slug)
    {
        $taggedUser = $this->tagUtil()->taggedUserModelString();
        $taggedUserIds = $taggedUser::select('tagging_tagged_user.*')
            ->join('tagging_tagged', 'tagging_tagged.id', '=', 'tagging_tagged_user.tagged_id')
            ->where('tagging_tagged.tag_slug', $this->tagUtil()->slug($slug))
            ->where('tagging_tagged.taggable_type', get_class($taggable))
            ->where('tagging_tagged.taggable_id', $taggable->getKey())
            ->where('tagging_tagged_user.user_id', $user->id)
            ->get()
            ->pluck('id')
            ->all();

        $taggedUser::destroy($taggedUserIds);
    }

    /**
     * Collection of TagsRepository, related to the current Model
     *
     * @param Model $taggable
     * @return \Illuminate\Database\Eloquent\Collection [TagsRepository]
     */
    public function tagsFor(Model $taggable)
    {
        $slugs = $this->tagSlugsFor($taggable);
        if (empty($slugs)) {
            return new Collection();
        }

        $tag = $this->tagUtil()->tagModelString();
        return $tag::whereIn('slug', $slugs)->get();
    }

    /**
     * Array of the tag names related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagNamesFor(Model $taggable)
    {
        return $this->taggedColFor($taggable, 'tag_name');
    }

    /**
     * Array of the tag slugs related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function tagSlugsFor(Model $taggable)
    {
        return $this->taggedColFor($taggable, 'tag_slug');
    }

    /**
     * Collection of Tagged for this Model
     *
     * @param Model $taggable
     * @return Collection [Tagged]
     */
    public function taggedFor(Model $taggable)
    {
        $tagged = $this->tagUtil()->taggedModelString();
        return $tagged::where('taggable_id', $taggable->getKey())
            ->where('taggable_type', get_class($taggable))
            ->get();
    }

    /**
     * Array of Tagged [__col__], related to the current Model
     *
     * @param Model $taggable
     * @param string $col
     * @return array [int]
     */
    public function taggedColFor(Model $taggable, $col = 'tag_slug')
    {
        $tagged = $this->tagUtil()->taggedModelString();
        return $tagged::where('taggable_id', $taggable->getKey())
            ->where('taggable_type', get_class($taggable))
            ->pluck($col)
            ->all();
    }

    /**
     * Array of Tagged [id], related to the current Model
     *
     * @param Model $taggable
     * @return array [int]
     */
    public function taggedIdsFor(Model $taggable)
    {
        return $this->taggedColFor($taggable, 'id');
    }

    /**
     * Collection of Tagged in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param Model $user
     * @return Collection [Tagged]
     */
    public function taggedForUser(Model $taggable, Model $user)
    {
        $tagged = $this->tagUtil()->taggedModelString();
        return $tagged::select('tagging_tagged.*')
            ->join('tagging_tagged_user AS tu', 'tu.tagged_id', '=', 'tagging_tagged.id')
            ->where('taggable_id', $taggable->getKey())
            ->where('taggable_type', get_class($taggable))
            ->where('tu.user_id', $user->id)
            ->groupBy('tu.id')
            ->get();
    }

    /**
     * Array of Tagged [ids] in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [int]
     */
    public function taggedIdsForUser(Model $taggable, Model $user)
    {
        $taggedIds = $this->taggedIdsFor($taggable);
        $tagged = $this->tagUtil()->taggedModelString();
        return $tagged::select('tagging_tagged.id AS id')
            ->join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
            ->whereIn('tagging_tagged.id', $taggedIds)
            ->where('ttu.user_id', '=', $user->id)
            ->pluck('id')
            ->all();
    }

    /**
     * Get a column from Tagged
     * 
     * @param Model $taggable
     * @param Model $user
     * @param string $col
     * @return array[int|string]
     */
    public function taggedColForUser(Model $taggable, Model $user, $col = 'tag_slug')
    {
        $taggedIds = $this->taggedIdsForUser($taggable, $user);
        $tagged = $this->tagUtil()->taggedModelString();
        return $tagged::select($col)
            ->join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
            ->whereIn('tagged_id', $taggedIds)
            ->where('user_id', $user->id)
            ->pluck($col)
            ->unique()
            ->all();
    }

    /**
     * Collection of Tag, which have been Tagged by User
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return Collection [Tag]
     */
    public function tagsForUser(Model $taggable, Model $user)
    {
        /** @var \Dan\Tagging\Repositories\Tags\TagsRepository $tag */
        $tags = app($this->tagUtil()->tagsRepositoryInterface());
        $slugs = $this->tagSlugsForUser($taggable, $user);
        return empty($slugs)
            ? new Collection()
            : $tags->findAllBy('slug', $slugs);
    }

    /**
     * Array of slugs in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagSlugsForUser(Model $taggable, Model $user)
    {
        return $this->taggedColForUser($taggable, $user, $col = 'tag_slug');
    }

    /**
     * Array of names in which a User has tagged this Model
     *
     * @param Model $taggable
     * @param \App\User|Model $user
     * @return array [string]
     */
    public function tagNamesForUser(Model $taggable, Model $user)
    {
        return $this->taggedColForUser($taggable, $user, $col = 'tag_name');
    }

    /**
     * Array of TaggedUser [user_id], related to the current Model
     *
     * @param Model $taggable
     * @return array
     */
    public function userIdsWhoTagged(Model $taggable)
    {
        $taggedIds = $this->taggedIdsFor($taggable);
        if (empty($taggedIds)) {
            return [];
        }

        $taggedUser = $this->tagUtil()->taggedUserModelString();
        return $taggedUser::whereIn('tagged_id', $taggedIds)
            ->pluck('user_id')
            ->unique()
            ->all();
    }

    /**
     * Collection of User who tagged, related to the current model
     *
     * @param Model $taggable
     * @return Collection
     */
    public function usersWhoTagged(Model $taggable)
    {
        $userIds = $this->userIdsWhoTagged($taggable);
        /** @var \App\User $user */
        $user = $this->tagUtil()->userModelString();
        return empty($userIds)
            ? new Collection()
            : $user::findMany($userIds);
    }

    /**
     * Has the User tagged the current Model?
     *
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @return bool
     */
    public function isTaggedByUser(Model $taggable, $user)
    {
        $userModel = $this->tagUtil()->userModel();
        $userId = $user instanceof $userModel ? $user->getKey() : $user;
        $tagged = $this->tagUtil()->taggedModelString();
        
        $query = $tagged::join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
            ->where('tagging_tagged.taggable_type', get_class($taggable))
            ->where('tagging_tagged.taggable_id', $taggable->getKey())
            ->where('ttu.user_id', $userId);
        
        return boolval($query->count());
    }

    /**
     * Has the User tagged the current Model with any of the $tags provided?
     *
     * @param mixed $tags
     * @param Model $taggable
     * @param \App\User|Model|int $user
     * @return bool
     */
    public function isTaggedByUserWith(Model $taggable, $user, $tags)
    {
        $userModel = $this->tagUtil()->userModel();
        $userId = $user instanceof $userModel ? $user->getKey() : $user;
        
        $tagged = $this->tagUtil()->taggedModelString();
        $slugs = $this->tagUtil()->slugArray($tags);

        $query = $tagged::select('tagging_tagged.*')
            ->join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
            ->where('tagging_tagged.taggable_type', get_class($taggable))
            ->where('tagging_tagged.taggable_id', $taggable->getKey())
            ->whereIn('tagging_tagged.tag_slug', $slugs)
            ->where('ttu.user_id', $userId);

        return boolval($query->count());
    }

}