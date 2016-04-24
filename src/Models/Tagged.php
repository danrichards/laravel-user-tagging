<?php

namespace Dan\Tagging\Models;

use Dan\Tagging\Traits\Util as TaggingUtility;
use Dan\Tagging\Traits\RepositoryFromModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tagged
 *
 * Copyright (C) 2014 Robert Conner
 */
class Tagged extends Model
{
	
	use RepositoryFromModel, TaggingUtility;
	
	/** @var string $table */
	protected $table = 'tagging_tagged';
	
	/** @var bool $timestamps */
	public $timestamps = false;
	
	/** @var array $fillable */
	protected $fillable = ['taggable_id', 'taggable_type', 'tag_name', 'tag_slug'];
	
	/**
	 * Morph to the tag
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function taggable()
	{
		return $this->morphTo();
	}
	
	/**
	 * Get instance of tag linked to the tagged value.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function tag()
	{
		return $this->belongsTo($this->tagUtil()->tagModelString(), 'tag_slug', 'slug');
	}

	/**
	 * Get the users tagged relations (tagged_tagged_user).
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function taggedUsersHasMany()
	{
		return $this->hasMany(
			$this->tagUtil()->taggedUserModelString(), 
			'tagged_id'
		);
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
		$util = $this->tagUtil();
		if (!is_array($tagNames)) {
			$tagNames = func_get_args();
			array_shift($tagNames);
		}
		$tagSlugs = $util::makeSlugArray($tagNames);
		foreach ($tagSlugs as $tagSlug) {
			$tags = $this->newQuery()
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
		$tagSlugs = $this->tagUtil()->slugArray($tagNames);
		$tags = $this->newQuery()
			->whereIn('tag_slug', $tagSlugs)
			->pluck('taggable_id');

		return $query->whereIn($this->getTable().'.id', $tags);
	}

	/**
	 * Update the user count on the Tagged model
	 */
	public function recalculate()
	{
		$this->users_count = $this->tagUtil()->taggedUserModel()
			->where('tagged_id', $this->id)->count();

		$this->save();
	}
	
}