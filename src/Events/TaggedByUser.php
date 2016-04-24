<?php

namespace Dan\Tagging\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

/**
 * Class TaggedByUser
 *
 * This event fires when the state of a user's tags for a given model, have
 * changed.
 *
 * Note: you can always extend Tag, Tagged, and TaggedUser models and use
 * the native Laravel Eloquent Model Events.
 *
 * @see ../../config/tagging.php
 * @see https://laravel.com/docs/5.2/eloquent#events
 */
class TaggedByUser
{

	use SerializesModels;

	/** @var \Illuminate\Database\Eloquent\Model **/
	public $taggable;

	/** @var \App\User $user */
	public $user;

	/** @var array */
	public $slugsAdded;

	/** @var array */
	public $slugsRemoved;

	/**
	 * Create a new event instance.
	 *
	 * @param Model $taggable
	 * @param Model $user
	 * @param array $slugsAdded
	 * @param array $slugsRemoved
	 */
	public function __construct(Model $taggable, Model $user, array $slugsAdded, array $slugsRemoved)
	{
		$this->taggable = $taggable;
		$this->user = $user;
		$this->slugsAdded = $slugsAdded;
		$this->slugsRemoved = $slugsRemoved;
	}
	
}