<?php namespace Conner\Tagging\Contracts;

/**
 * Copyright (C) 2015 Robert Conner
 */
interface TaggableContract {

	/**
	 * Return collection of tagged rows related to the tagged model
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function tagged();

	/**
	 * Perform the action of tagging the model with the given string
	 *
	 * @param $tagNames string or array
	 */
	public function tag($tagNames);
	
	/**
	 * Return array of the tag names related to the current model
	 *
	 * @return array
	 */
	public function tagNames();

	/**
	 * Return array of the tag slugs related to the current model
	 *
	 * @return array
	 */
	public function tagSlugs();
	
	/**
	 * Remove the tag from this model
	 *
	 * @param string|array|null $tagNames (null to remove all tags)
	 */
	public function untag($tagNames = null);
	
	/**
	 * Replace the tags from this model
	 *
	 * @param string|array $tagNames
	 */
	public function retag($tagNames);

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param \Illuminate\Database\Query\Builder $query
	 * @param array|string $tagNames
	 */
	public function scopeWithAllTags($query, $tagNames);

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param \Illuminate\Database\Query\Builder $query
	 * @param array|string $tagNames
	 */
	public function scopeWithAnyTag($query, $tagNames);
	
	/**
	 * Return an array of all of the tags that are in use by this model
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function existingTags();
	
	/**
	 * Delete tags that are not used anymore
	 */
	public static function untagOnDelete();
	
	/**
	 * Should untag on delete
	 */
	public static function shouldDeleteUnused();

}
