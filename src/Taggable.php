<?php

namespace Conner\Tagging;

use App\User;
use Conner\Tagging\Contracts\TaggingUtility;
use Conner\Tagging\Events\TagAdded;
use Conner\Tagging\Events\TagRemoved;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Copyright (C) 2014 Robert Conner
 *
 * @self \Illuminate\Database\Eloquent\Model
 */
trait Taggable {

	/** @var \Conner\Tagging\Contracts\TaggingUtility **/
	static $taggingUtility;
	
	/**
	 * Boot the soft taggable trait for a model.
	 *
	 * @return void
	 */
	public static function bootTaggable()
	{
		static::$taggingUtility = app(TaggingUtility::class);

		if (static::untagOnDelete()) {
			static::deleting(function($model) {
				$model->untag();
			});
		}
	}

	/**
	 * Return collection of tags related to the tagged model
	 * TODO : I'm sure there is a faster way to build this, but
	 * If anyone knows how to do that, me love you long time.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getTagsAttribute()
	{
		return $this->tagged->map(function($item){
			return $item->tag;
		});
	}

	/**
	 * Perform the action of tagging the model with the given string
	 *
	 * @param $tagNames
	 * @param User $user
	 * @internal param string $tagName or array
	 */
	public function tag($tagNames, User $user)
	{
		$util = static::$taggingUtility;
		$tagNames = $util::makeTagArray($tagNames);
		
		foreach($tagNames as $tagName) {
			$this->addTag($tagName, $user);
		}
	}

	/**
	 * Remove the tag from this model
	 *
	 * @param null $tagNames
	 * @param User $user
	 */
	public function untag($tagNames = null, User $user)
	{
		$util = static::$taggingUtility;
		if (is_null($tagNames)) {
			$tagNames = $this->tagNames();
		}
		
		$tagNames = $util::makeTagArray($tagNames);
		
		foreach ($tagNames as $tagName) {
			$this->removeTag($tagName, $user);
		}
		
		if (static::shouldDeleteUnused()) {
			$util->deleteUnusedTags();
		}
	}

	/**
	 * Replace the tags from this model
	 *
	 * @param $tagNames
	 * @param User $user
	 * @internal param string $tagName or array
	 */
	public function retag($tagNames, User $user)
	{
		/** @var TaggingUtility $util */
		$util = static::$taggingUtility;
		$tagNames = $util::makeTagArray($tagNames);
		$currentTagNames = $this->tagNames();
		
		$deletions = array_diff($currentTagNames, $tagNames);
		$additions = array_diff($tagNames, $currentTagNames);
		
		$this->untag($deletions, $user);

		foreach ($additions as $tagName) {
			$this->addTag($tagName, $user);
		}
	}

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param $tagNames array|string
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWithAllTags($query, $tagNames)
	{
		$util = static::$taggingUtility;
		if (!is_array($tagNames)) {
			$tagNames = func_get_args();
			array_shift($tagNames);
		}
		$tagSlugs = $util::makeSlugArray($tagNames);
		$tagged = app($util::taggedModelString());
		foreach ($tagSlugs as $tagSlug) {
			$tags = $tagged->newQuery()
				->where('tag_slug', $tagSlug)
				->where('taggable_type', __CLASS__)
				->pluck('taggable_id');
		
			$query->whereIn($this->getTable().'.id', $tags);
		}
		
		return $query;
	}

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param $tagNames array|string
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWithAnyTag($query, $tagNames)
	{
		$util = static::$taggingUtility;
		if (!is_array($tagNames)) {
			$tagNames = func_get_args();
			array_shift($tagNames);
		}
		$tagSlugs = $util::makeSlugArray($tagNames);

		$tagged = app($util::taggedModelString());
		$tags = $tagged->newQuery()
			->whereIn('tag_slug', $tagSlugs)
			->where('taggable_type', __CLASS__)
			->pluck('taggable_id');
		
		return $query->whereIn($this->getTable().'.id', $tags);
	}

	/**
	 * Adds a single tag
	 *
	 * @param $tagName string
	 * @param User $user
	 */
	private function addTag($tagName, User $user = null)
	{
		$util = static::$taggingUtility;
		$tagName = trim($tagName);
		/** @var \Conner\Tagging\Model\Tag $tag */
		$tag = $util::tagModelString();
		$tag = call_user_func([$tag, 'findOrCreate'], $tagName);

		if ($this->isTaggedByWith($tag->slug, $user)) {
			return;		// Nothing to do here.
		}

		/** @var \Conner\Tagging\Model\Tagged $tagged */
		$tagged = app($util::taggedModelString());
		$tagged = $tagged::where('tag_slug', '=', $tag->slug)->first();
		if (empty($tagged)) {
			$tagged = app($util::taggedModelString());
			$tagged->tag_name = $util::display($tagName);
			$tagged->tag_slug = $tag->slug;
			$tagged = $this->tagged()->save($tagged);
			$tag->recalculate();
		}

		$taggedUser = app($util::taggedUserModelString());
		$taggedUser->tagged_id = $tagged->id;
		$taggedUser->user_id = $user->id;
		$taggedUser->save();

		$tagged->recalculate();

		event(new TagAdded($this, $user));
	}

	/**
	 * Removes a single tag
	 *
	 * @param $tagName string
	 * @param User $user
	 */
	private function removeTag($tagName, User $user)
	{
		$util = static::$taggingUtility;
		$tagSlug = $util::normalize($tagName);

		$taggedUser = app($util::taggedUserModelString());
		$taggedUser::join('tagging_tagged AS tt', 'tt.id', '=', 'tagging_tagged_user.tagged_id')
			->where('tt.tag_slug', $tagSlug)
			->delete();
		
		event(new TagRemoved($this, $user));
	}
	
	/**
	 * Delete tags that are not used anymore?
	 * 
	 * @return bool
	 */
	public static function untagOnDelete()
	{
		return isset(static::$untagOnDelete)
			? static::$untagOnDelete
			: config('tagging.untag_on_delete', true);
	}
	
	/**
	 * Untag on delete?
	 *
	 * @return bool
	 */
	public static function shouldDeleteUnused()
	{
		return config('tagging.delete_unused_tags', true);
	}

	/**
	 * Collection of Tag(sort of), related to the current Model
	 *
	 * @return Collection
	 */
	public static function existingTags()
	{
		$util = static::$taggingUtility;
		$tagged = $util::taggedModelString();
		return $tagged::distinct()
			->join('tagging_tags', 'tag_slug', '=', 'tagging_tags.slug')
			->where('taggable_type', '=', __CLASS__)
			->orderBy('tag_slug', 'ASC')
			->get(['tag_slug AS slug', 'tag_name AS name', 'tagging_tags.count AS count']);
	}

	/**
	 * Collection of Tag, related to the current Model
	 *
	 * @return \Illuminate\Database\Eloquent\Collection [Tag]
	 */
	public function tags()
	{
		$util = static::$taggingUtility;
		$slugs = $this->tagSlugs();
		if (empty($slugs)) {
			return new Collection();
		}
		$tag = app($util::tagModelString());
		return $tag::whereIn('slug', $slugs)->get();
	}

	/**
	 * Collection of Tagged, related to the current Model
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function tagged()
	{
		$util = static::$taggingUtility;
		return $this->morphMany($util::taggedModelString(), 'taggable');
	}

	/**
	 * Array of the tag names related to the current Model
	 *
	 * @return array
	 */
	public function tagNames()
	{
		return $this->taggedCol('tag_name');
	}

	/**
	 * Array of the tag slugs related to the current Model
	 *
	 * @return array
	 */
	public function tagSlugs()
	{
		return $this->taggedCol('tag_slug');
	}

	/**
	 * Array of Tagged [__col__], related to the current Model
	 *
	 * @param string $col
	 * @return array [int]
	 */
	public function taggedCol($col = 'slug')
	{
		$util = static::$taggingUtility;
		$tagged = app($util::taggedModelString());
		return $tagged::where('taggable_id', $this->getKey())
			->where('taggable_type', __CLASS__)
			->pluck($col)
			->all();
	}

	/**
	 * Array of Tagged [id], related to the current Model
	 *
	 * @return array [int]
	 */
	public function taggedIds()
	{
		return $this->taggedCol('id');
	}

	/**
	 * Array of TaggedUser [ids] in which a User has tagged this Model
	 *
	 * @param User $user
	 * @return array[int]
	 */
	public function taggedIdsFor(User $user)
	{
		$util = static::$taggingUtility;
		$taggedIds = $this->taggedIds();
		$tagged = app($util::taggedModelString());
		return $tagged::select('tagging_tagged.id AS id')
			->join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
			->whereIn('tagging_tagged.id', $taggedIds)
			->where('ttu.user_id', '=', $user->id)
			->pluck('id')
			->all();
	}

	/**
	 * Collection of Tag, which have been Tagged by User
	 *
	 * @param User $user
	 * @return Collection [Tagged]
	 */
	public function tagsFor(User $user)
	{
		$util = static::$taggingUtility;
		/** @var \Conner\Tagging\Model\Tag $tag */
		$tag = $util::tagModelString();
		$slugs = $this->tagSlugsFor($user);
		return empty($slugs)
			? new Collection()
			: $tag::findTags($slugs);
	}

	/**
	 * Array of Tagged [id] in which a User has tagged this Model
	 *
	 * @param User $user
	 * @return array[string]
	 */
	public function tagSlugsFor(User $user)
	{
		$util = static::$taggingUtility;
		$taggedIds = $this->taggedIds();
		$tagged = app($util::taggedModelString());
		return $tagged::select('tag_slug')
			->join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
			->whereIn('tagged_id', $taggedIds)
			->where('user_id', $user->id)
			->pluck('tag_slug')
			->unique()
			->all();
	}

	/**
	 * Array of TaggedUser [user_id], related to the current Model
	 *
	 * @return array
	 */
	public function userIdsWhoTagged()
	{
		$taggedIds = $this->taggedIds();
		if (empty($taggedIds)) {
			return [];
		}

		$util = static::$taggingUtility;
		/** @var \Conner\Tagging\Model\TaggedUser $tu */
		$tu = app($util::taggedUserModelString());
		return $tu::whereIn('tagged_id', $taggedIds)
			->pluck('user_id')
			->unique();
	}

	/**
	 * Collection of User who tagged, related to the current model
	 *
	 * @return Collection
	 */
	public function usersWhoTagged()
	{
		$util = static::$taggingUtility;
		/** @var User $user */
		$user = app($util::userModelString());
		$userIds = $this->userIdsWhoTagged();
		return empty($userIds)
			? new Collection()
			: $user::findMany($userIds);
	}

	/**
	 * Has the User tagged the current Model?
	 *
	 * @param User $user
	 * @return bool
	 */
	public function isTaggedBy(User $user)
	{
		$util = static::$taggingUtility;
		$tagged = app($util::taggedModelString());
		$query = $tagged::join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
			->where('tagging_tagged.taggable_type', __CLASS__)
			->where('tagging_tagged.taggable_id', $this->getKey())
			->where('ttu.user_id', $user->id);
		return boolval($query->count());
	}

	/**
	 * Has the User tagged the current Model with any of the $tags provided?
	 *
	 * @param mixed $tags
	 * @param User $user
	 * @return bool
	 */
	public function isTaggedByWith($tags, User $user)
	{
		$util = static::$taggingUtility;
		$tagged = app($util::taggedModelString());
		$slugs = $util::makeSlugArray($tags);

		$query = $tagged::join('tagging_tagged_user AS ttu', 'ttu.tagged_id', '=', 'tagging_tagged.id')
			->where('tagging_tagged.taggable_type', __CLASS__)
			->where('tagging_tagged.taggable_id', $this->getKey())
			->whereIn('tagging_tagged.tag_slug', $slugs)
			->where('ttu.user_id', $user->id);

		return boolval($query->count());
	}

}
