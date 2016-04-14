<?php

namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class Tag
 *
 * Copyright (C) 2014 Robert Conner
 */
class Tag extends Eloquent
{
	
	/** @var string $table */
	protected $table = 'tagging_tags';
	
	/** @var bool $timestamps */
	public $timestamps = false;
	
	/** @var bool $softDelete */
	protected $softDelete = false;
	
	/** @var array $fillable */
	public $fillable = ['name'];

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
	 * (non-PHPdoc)
	 * @see \Illuminate\Database\Eloquent\Model::save()
	 * @param array $options
	 * @return bool
	 * @throws \Exception
	 */
	public function save(array $options = array())
	{
		$validator = Validator::make(
			array('name' => $this->name),
			array('name' => 'required|min:1')
		);
		
		if($validator->passes()) {
			$normalizer = config('tagging.normalizer');
			$normalizer = $normalizer ?: [$this->taggingUtility, 'slug'];
			
			$this->slug = call_user_func($normalizer, $this->name);
			return parent::save($options);
		} else {
			throw new \Exception('Tag Name is required');
		}
	}

	/**
	 * Get a Tag model or create a new one if it does not exist.
	 *
	 * @param $name
	 * @return Tag|null
	 */
	public static function findOrCreate($name)
	{
		if (! empty($tag = self::findTag($name))) {
			return $tag;
		}
		$util = app(TaggingUtility::class);
		$slug = call_user_func([$util, 'normalize'], $name);
		$tag = new static(compact('name', 'slug'));
		$tag->save();
		return $tag;
	}

	/**
	 * Get a Tag model by slug.
	 *
	 * @param mixed $tag
	 * @return Tag|null
	 */
	public static function findTag($tag)
	{
		/** @var TaggingUtility $util */
		$util = app(TaggingUtility::class);
		$tag = current($util::makeSlugArray($tag));
		return self::where('slug', $tag)->first();
	}


	/**
	 * Get many Tag models by an array of slugs.
	 *
	 * @param mixed $tags
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function findTags($tags)
	{
		/** @var TaggingUtility $util */
		$util = app(TaggingUtility::class);
		$tags = $util::makeSlugArray($tags);
		return self::whereIn('slug', $tags)->get();
	}

	/**
	 * Update the count on the Tag model.
	 *
	 * @return $this
	 */
	public function recalculate()
	{
		$util = $this->taggingUtility;
		/** @var \Conner\Tagging\Model\Tagged $model */
		$tagged = app($util::taggedModelString());
		$this->count = $tagged::where('tag_slug', $this->slug)->count();
		$this->save();
		return $this;
	}

	/**
	 * Get suggested tags
	 *
	 * @param $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeSuggested($query)
	{
		return $query->where('suggest', true);
	}
	
	/**
	 * Set the name of the tag : $tag->name = 'myname';
	 *
	 * @param string $value
	 */
	public function setNameAttribute($value)
	{
		$displayer = config('tagging.displayer');
		$displayer = empty($displayer) ? '\Illuminate\Support\Str::title' : $displayer;
		
		$this->attributes['name'] = call_user_func($displayer, $value);
	}

	/**
	 * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
	 * Does not delete tags where 'suggest' value is true
	 *
	 * @return int
	 */
	public static function deleteUnused()
	{
		return (new static)->newQuery()
				->where('count', '=', 0)
				->where('suggest', false)
				->delete();
	}

	/**
	 * Users who have tagged something with this tag.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function users()
	{
		// get ids of the Tagged
		$util = $this->taggingUtility;
		$tagged = app($util::taggedModelString());
		$taggedIds = $tagged::where('tag_slug', $this->slug)->pluck('id')->all();

		if (empty($taggedIds)) {
			return new Collection();
		}

		// get userIds of TaggedUser
		$taggedUser = app($util::taggedUserModelString());
		$usersIds = $taggedUser::whereIn('id', $taggedIds)->pluck('user_id')->all();
		$usersIds = array_unique($usersIds);

		/** @var \App\User $user */
		$user = app($util::userModelString());
		return $user::findMany($usersIds);
	}
	
}
