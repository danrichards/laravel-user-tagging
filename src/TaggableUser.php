<?php

namespace Conner\Tagging;

use Conner\Tagging\Contracts\TaggingUtility;
use Conner\Tagging\Model\Tag;

/**
 * Trait TaggableUser
 *
 * @self \App\User
 */
trait TaggableUser {

	/** @var \Conner\Tagging\Contracts\TaggingUtility **/
	static $taggingUtility;
	
	/**
	 * Boot the soft taggable trait for a model.
	 *
	 * @return void
	 */
	public static function bootTaggableUser()
	{
		static::$taggingUtility = app(TaggingUtility::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function tagged()
	{
		$util = static::$taggingUtility;
		return $this->belongsToMany($util::taggedModelString(), 'tagging_tagged_user', 'user_id', 'tagged_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection [Tag]
	 */
	public function tags()
	{
		return Tag::findTags($this->tagged()->groupBy('tag_slug')->get());
	}

	/**
	 * Models that have been tagged by the user.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection [Model]
	 */
	public function taggedModels()
	{
		$tagged = $this->tagged()->groupBy('taggable_id')->get();
		return $tagged->map(function($item) {
			return $item->taggable()->get()->first();
		});
	}

}
