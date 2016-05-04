<?php

namespace Dan\Tagging\Contracts;

/**
 * Interface of utility functions to help with various tagging functionality.
 *
 * @author Dan Richards <danrichardsri@gmail.com>
 * @author Rob Conner <rtconner+gh@gmail.com>
 *
 * Copyright (C) 2015 Robert Conner
 */
interface TaggingUtility
{

	/**
	 * Slug from a string with the instance.
	 *
	 * @param string $name
	 * @return string
	 */
	public function slug($name);

	/**
	 * Make a slug from a string
	 *
	 * @param string $name
	 * @return string
	 */
	public static function makeSlug($name);

	/**
	 * Array of slugs from various inputs
	 *
	 * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|\stdClass|array|string $tags
	 * @return array [string]
	 * @throws \InvalidArgumentException
	 */
	public function slugArray($tags);

	/**
	 * Make an array of slugs from various inputs
	 *
	 * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|\stdClass|array|string $tags
	 * @return array [string]
	 * @throws \InvalidArgumentException
	 */
	public static function makeSlugArray($tags);

	/**
	 * Title from a string with the instance
	 *
	 * @param string $name
	 * @return string
	 */
	public function title($name);

	/**
	 * Make a title from a string with static
	 *
	 * @param string $name
	 * @return string
	 */
	public static function makeTitle($name);

	/**
	 * Array of titles from various inputs
	 *
	 * @param array|string $names
	 * @return array
	 */
	public function titleArray($names);

	/**
	 * Make an array of titles from various inputs
	 *
	 * @param array|string $names
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public static function makeTitleArray($names);

	/**
	 * Fetch a user for an int or return the User model provided.
	 *
	 * @param $user
	 * @return \App\User|\Illuminate\Database\Eloquent\Model
	 */
	public function user($user);

	/**
	 * Fetch a user id for a Model or return the user id provided.
	 *
	 * @param $user
	 * @return \App\User|\Illuminate\Database\Eloquent\Model
	 */
	public function userId($user);

	/**
	 * @return string
	 */
	public function usersRepositoryInterface();

	/**
	 * @param null $args
	 * @return \App\User
	 */
	public function userModel($args = null);

	/**
	 * @return string
	 */
	public function userModelString();

	/**
	 * @return string
	 */
	public static function getUserModelString();

	/**
	 * @return string
	 */
	public function tagsRepositoryInterface();

	/**
	 * @return string
	 */
	public function tagModelString();

	/**
	 * @return string
	 */
	public static function getTagModelString();

	/**
	 * @param null $args
	 * @return \Dan\Tagging\Models\Tag
	 */
	public function tagModel($args = null);

	/**
	 * @return string
	 */
	public function taggedRepositoryInterface();

	/**
	 * @param null $args
	 * @return \Dan\Tagging\Models\Tagged
	 */
	public function taggedModel($args = null);

	/**
	 * @return string
	 */
	public function taggedModelString();

	/**
	 * @return string
	 */
	public static function getTaggedModelString();

	/**
	 * @return \Dan\Tagging\Repositories\TaggedUser\TaggedUserRepository
	 */
	public function taggedUserRepositoryInterface();

	/**
	 * @param null $args
	 * @return \Dan\Tagging\Models\TaggedUser
	 */
	public function taggedUserModel($args = null);

	/**
	 * @return string
	 */
	public function taggedUserModelString();

	/**
	 * @return string
	 */
	public static function getTaggedUserModelString();

	/**
	 * @param \Illuminate\Database\Eloquent\Model|string $model
	 * @return string
	 */
	public function taggableRepositoryInterface($model);

}
