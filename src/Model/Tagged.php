<?php

namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Tagged
 *
 * Copyright (C) 2014 Robert Conner
 */
class Tagged extends Eloquent
{
	
	/** @var string $table */
	protected $table = 'tagging_tagged';
	
	/** @var bool $timestamps */
	public $timestamps = false;
	
	/** @var array $fillable */
	protected $fillable = ['tag_name', 'tag_slug'];
	
	/** @var TaggingUtility $taggingUtility */
	protected $taggingUtility;

	/**
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);		
		$this->taggingUtility = app(TaggingUtility::class);
	}
	
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
		$util = $this->taggingUtility;
		$tag = $util::tagModelString();
		return $this->belongsTo($tag, 'tag_slug', 'slug');
	}

	/**
	 * Get the users who have tagged this model.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection [User]
	 */
	public function users()
	{
		$util = $this->taggingUtility;
		$tu = $util::taggedUserModelString();
		$userIds = $tu::select('user_id')
			->where("tagged_id", $this->id)
			->pluck('user_id')
			->all();
		$user = app($util::userModelString());
		return $user::findMany($userIds);
	}

	/**
	 * Get the users tagged relations (tagged_tagged_user).
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function taggedUsers()
	{
		$util = $this->taggingUtility;
		$tu = $util::taggedUserModelString();
		return $this->hasMany($tu, 'tagged_id');
	}

	/**
	 * Update the user count on the Tagged model
	 * 
	 * @return $this
	 */
	public function recalculate()
	{
		$util = $this->taggingUtility;
		/** @var \Conner\Tagging\Model\TaggedUser $taggedUser */
		$tu = app($util::taggedUserModelString());
		$this->users_count = $tu::where('tagged_id', $this->id)->count();
		$this->save();
		return $this;
	}
	
}