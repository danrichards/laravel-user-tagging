<?php

namespace Conner\Tagging\Contracts;

/**
 * Interface of utility functions to help with various tagging functionality.
 *
 * @author Rob Conner <rtconner+gh@gmail.com>
 *
 * Copyright (C) 2015 Robert Conner
 */
interface TaggingUtility
{
	
	/**
	 * Converts input into array
	 *
	 * @param array|string $tagNames
	 * @return array
	 */
	public static function makeTagArray($tagNames);

	/**
	 * Build an array of slugs with various inputs.
	 *
	 * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\stdClass|array|string $tags
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public static function makeSlugArray($tags);

	/**
	 * Create a web friendly URL slug from a string.
	 *
	 * Although supported, transliteration is discouraged because
	 * 1) most web browsers support UTF-8 characters in URLs
	 * 2) transliteration causes a loss of information
	 *
	 * @author Sean Murphy <sean@iamseanmurphy.com>
	 *
	 * @param string $str
	 * @return string
	 */
	public static function slug($str);
	
	/**
	 * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
	 * Does not delete tags where 'suggest' is true
	 *
	 * @return int
	 */
	public function deleteUnusedTags();

	/**
	 * Return string with full namespace of the User model
	 *
	 * @return string
	 */
	public static function userModelString();
	
	/**
	 * Return string with full namespace of the Tag model
	 *
	 * @return string
	 */
	public static function tagModelString();

	/**
	 * Return string with full namespace of the Tagged model
	 *
	 * @return string
	 */
	public static function taggedModelString();

	/**
	 * Return string with full namespace of the TaggedUser model
	 *
	 * @return string
	 */
	public static function taggedUserModelString();

	/**
	 * Return normalized string or array.
	 *
	 * @param array|string $tagNames
	 * @return array|string
	 */
	public static function normalize($tagNames);

	/**
	 * Return display constraint for string or array.
	 *
	 * @param array|string $tagNames
	 * @return array|string
	 */
	public static function display($tagNames);

}
