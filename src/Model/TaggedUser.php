<?php namespace Conner\Tagging\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserTagged extends Eloquent
{
	protected $table = 'tagging_tagged_user';

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}
}