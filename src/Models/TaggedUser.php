<?php

namespace Dan\Tagging\Models;

use Dan\Tagging\Traits\Util as TaggingUtility;
use Dan\Tagging\Traits\RepositoryFromModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TaggedUser
 */
class TaggedUser extends Model
{

	use RepositoryFromModel, TaggingUtility;
	
	/** @var string $table */
	protected $table = 'tagging_tagged_user';

	/** @var bool $timestamps */
	public $timestamps = true;

	/** @var array $fillable */
	public $fillable = ['tagged_id', 'user_id'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user()
	{
		return $this->hasOne($this->tagUtil()->userModelString(), 'id', 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function tagged()
	{
		return $this->hasOne($this->tagUtil()->taggedModelString(), 'id', 'tagged_id');
	}
	
}