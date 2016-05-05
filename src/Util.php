<?php

namespace Dan\Tagging;

use Dan\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class Util
 *
 * Functions to help with various tagging functionality.
 *
 * @author Dan Richards <danrichardsri@gmail.com>
 * @author Rob Conner <rtconner@gmail.com>
 * 
 * Copyright (C) 2014 Robert Conner
 */
class Util implements TaggingUtility
{

	/** Default method used to slug tags before persistence */
	const DEFAULT_NORMALIZER = '\Illuminate\Support\Str::slug';

	/** Default method used to title tags before persistence */
	const DEFAULT_DISPLAYER = '\Illuminate\Support\Str::title';

	/** @var callable $displayer */
	private $displayer;

	/** @var callable $normalizer */
	private $normalizer;

	/** @var string $usersRepositoryInterface */
	private $usersRepositoryInterface;

	/** @var string $userModelString */
	private $userModelString;

	/** @var string $tagsRepositoryInterface */
	private $tagsRepositoryInterface;

	/** @var string $tagModelString */
	private $tagModelString;

	/** @var string $taggedRepositoryInterface */
	private $taggedRepositoryInterface;

	/** @var string $taggedModelString */
	private $taggedModelString;

	/** @var string $taggedUserRepositoryInterface */
	private $taggedUserRepositoryInterface;

	/** @var string $taggedUserModelString */
	private $taggedUserModelString;

	/** @var array $taggableInterfaces */
	private $taggableInterfaces;

	/**
	 * Slug from Laravel's Str::slug or configurable slugging util.
	 *
	 * @param string $name
	 * @return string
	 */
	public function slug($name)
	{
		if (is_null($this->normalizer)) {
			$this->normalizer = config('tagging.normalizer', self::DEFAULT_NORMALIZER);
		}
		return call_user_func($this->normalizer, $name);
	}

	/**
	 * Make slug with Laravel's Str::slug or configurable slugging util.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function makeSlug($name)
	{
		$normalizer = config('tagging.normalizer', self::DEFAULT_NORMALIZER);
		return call_user_func($normalizer, trim($name));
	}

	/**
	 * Array of slugs from various inputs
	 *
	 * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|\stdClass|array|string $tags
	 * @return array [string]
	 * @throws InvalidArgumentException
	 */
	public function slugArray($tags)
	{
		return Util::makeSlugArray($tags);
	}

	/**
	 * Array of slugs from various inputs.
	 *
	 * @param \Illuminate\Support\Collection|Model|\stdClass|array|string $tags
	 * @return array [string]
	 * @throws InvalidArgumentException
	 */
	public static function makeSlugArray($tags) {
		// A string, possibly comma delimited, possibly still names
		if (is_string($tags)) {
			$names = static::makeTitleArray($tags);
			$util = new static;
			return array_map(function ($name) use ($util) {
				return $util->slug($name);
			}, $names);
		// An array of strings, possibly still names
		} elseif (is_array($tags)) {
			$names = static::makeTitleArray($tags);
			$util = new static;
			return array_map(function ($name) use ($util) {
				return $util->slug($name);
			}, $names);
		} elseif ($tags instanceof \Illuminate\Support\Collection) {
			if (is_object($first = $tags->first())){
				if (! empty($first->tag_slug)) {
					return $tags->pluck('tag_slug')->all();
				}
				if(! empty($first->slug)) {
					return $tags->pluck('slug')->all();
				}
				throw new InvalidArgumentException('Collection must have slug or tag_slug key.');
			} else {
				return [];
			}
		} elseif (is_object($tags) && ! empty($tags->slug)) {
			return (array) $tags->slug;
		} elseif (is_object($tags) && ! empty($tags->tag_slug)) {
			return (array)$tags->tag_slug;
		}

		throw new InvalidArgumentException('The $tags argument must be Collection|array|string or class with slug attribute|property.');
	}

	/**
	 * Title with Laravel's Str::title or configurable title util.
	 *
	 * @param string $name
	 * @return string
	 */
	public function title($name)
	{
		if (is_null($this->displayer)) {
			$this->displayer = config('tagging.displayer', self::DEFAULT_DISPLAYER);
		}
		return call_user_func($this->displayer, $name);
	}

	/**
	 * Make slug with Laravel's Str::title or configurable title util.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function makeTitle($name)
	{
		$displayer = config('tagging.displayer', self::DEFAULT_DISPLAYER);
		return call_user_func($displayer, [$name]);
	}

	/**
	 * Array of titles from various inputs
	 *
	 * @param array|string $names
	 * @return array
	 */
	public function titleArray($names)
	{
		return static::makeTitleArray($names);
	}

	/**
	 * Array of titles with various inputs.
	 *
	 * @param array|string $names
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public static function makeTitleArray($names)
	{
		if (is_string($names)) {
			$names = array_map('trim', explode(',', $names));
		}
		$util = new self;
		return array_map(function($name) use ($util) {
			return $util->title($name);
		}, $names);
	}

	/**
	 * Fetch a user for an int or return the User model provided.
	 *
	 * @param $user
	 * @return \App\User|\Illuminate\Database\Eloquent\Model
	 */
	public function user($user)
	{
		$userModel = $this->userModelString();
		if (is_object($user) && $user instanceof $userModel) {
			return $user;
		}
		
		if (is_numeric($user)) {
			/** @var \App\Gistribute\Repositories\Users\UsersRepository $users */
			$users = app($this->usersRepositoryInterface());
			return $users->find($user);
		}

		throw new InvalidArgumentException("Integer user id or instanceof {$userModel} required.");
	}

	/**
	 * Fetch a user for an int or return the User model provided.
	 *
	 * @param $user
	 * @return \App\User|\Illuminate\Database\Eloquent\Model
	 */
	public function userId($user)
	{
		if (is_numeric($user)) {
			return $user;
		}

		$userModel = $this->userModelString();
		if ($user instanceof $userModel) {
			return $user->getKey();
		}

		throw new InvalidArgumentException("Integer user id or instanceof {$userModel} required.");
	}

	/**
	 * @return string
	 */
	public function usersRepositoryInterface()
	{
		return is_null($this->usersRepositoryInterface)
			? $this->usersRepositoryInterface = ltrim(config('tagging.users_interface', 'Dan\Tagging\Repositories\Users\UsersRepositoryInterface'), '\\')
			: $this->usersRepositoryInterface;
	}

	/**
	 * @param null $args
	 * @return \App\User
	 */
	public function userModel($args = null)
	{
		return app($this->userModelString(), func_get_args());
	}

	/**
	 * @return string
	 */
	public function userModelString()
	{
		return is_null($this->tagModelString)
			? $this->userModelString = static::getUserModelString()
			: $this->userModelString;
	}

	/**
	 * @return string
	 */
	public static function getUserModelString()
	{
		return ltrim(config('tagging.user_model', 'App\User'), '\\');
	}

	/**
	 * @return string
	 */
	public function tagsRepositoryInterface()
	{
		return is_null($this->tagsRepositoryInterface)
			? $this->tagsRepositoryInterface = ltrim(config('tagging.tags_interface', 'Dan\Tagging\Repositories\Tags\TagsInterface'), '\\')
			: $this->tagsRepositoryInterface;
	}

	/**
	 * @param null $args
	 * @return Models\Tag
	 */
	public function tagModel($args = null)
	{
		return app($this->tagModelString(), func_get_args());
	}

	/**
	 * @return string
	 */
	public function tagModelString()
	{
		return is_null($this->tagModelString)
			? $this->tagModelString = ltrim(static::getTagModelString(), '\\')
			: $this->tagModelString;
	}

	/**
	 * @return string
	 */
	public static function getTagModelString()
	{
		return ltrim(config('tagging.tag_model', 'Dan\Tagging\Models\Tag'), '\\');
	}

	/**
	 * @return \Dan\Tagging\Repositories\Tagged\TaggedRepository
	 */
	public function taggedRepositoryInterface()
	{
		return is_null($this->taggedRepositoryInterface)
			? $this->taggedRepositoryInterface = ltrim(config('tagging.tagged_interface', 'Dan\Tagging\Repositories\Tagged\TaggedInterface'), '\\')
			: $this->taggedRepositoryInterface;
	}

	/**
	 * @param null $args
	 * @return Models\Tagged
	 */
	public function taggedModel($args = null)
	{
		return app($this->taggedModelString(), func_get_args());
	}

	/**
	 * @return string
	 */
	public function taggedModelString()
	{
		return is_null($this->taggedModelString)
			? $this->taggedModelString = ltrim(static::getTaggedModelString(), '\\')
			: $this->taggedModelString;
	}

	/**
	 * @return string
	 */
	public static function getTaggedModelString()
	{
		return ltrim(config('tagging.tagged_model', 'Dan\Tagging\Models\Tagged'), '\\');
	}

	/**
	 * @return \Dan\Tagging\Repositories\TaggedUser\TaggedUserRepository
	 */
	public function taggedUserRepositoryInterface()
	{
		return is_null($this->taggedUserRepositoryInterface)
			? $this->taggedUserRepositoryInterface = ltrim(config('tagging.tagged_user_interface', 'Dan\Tagging\Repositories\TaggedUser\TaggedUserInterface'), '\\')
			: $this->taggedUserRepositoryInterface;
	}

	/**
	 * @param null $args
	 * @return Models\TaggedUser
	 */
	public function taggedUserModel($args = null)
	{
		return app($this->taggedUserModelString(), func_get_args());
	}

	/**
	 * @return string
	 */
	public function taggedUserModelString()
	{
		return is_null($this->taggedUserModelString)
			? $this->taggedUserModelString = ltrim(static::getTaggedUserModelString(), '\\')
			: $this->taggedUserModelString;
	}

	/**
	 * @return string
	 */
	public static function getTaggedUserModelString()
	{
		return ltrim(config('tagging.tagged_user_model', 'Dan\Tagging\Models\TaggedUser'), '\\');
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model|string $model
	 * @return Repositories\Taggable\TaggableInterface
	 */
	public function taggableRepositoryInterface($model)
	{
		if (empty($this->taggableInterfaces)) {
			$this->taggableInterfaces = config('tagging.taggable_interfaces', []);
		}

		$model = $model instanceof Model ? get_class($model) : $model;

		foreach ($this->taggableInterfaces as $m => $interface) {
			if (ltrim($model, '\\') == ltrim($m, '\\')) {
				return $interface;
			}
		}
		
		throw new InvalidArgumentException("Interface for $model could not be resolved.");
	}

}
