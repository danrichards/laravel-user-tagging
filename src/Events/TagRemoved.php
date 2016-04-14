<?php namespace Conner\Tagging\Events;

use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class TagRemoved
{
	use SerializesModels;

	/** @var \Illuminate\Database\Eloquent\Model **/
	public $model;

	/** @var  \App\User $user */
	public $user;

	/**
	 * Create a new event instance.
	 *
	 * @param Model $model
	 * @param $user
	 */
	public function __construct(Model $model, User $user)
	{
		$this->model = $model;
		$this->user = $user;
	}
}