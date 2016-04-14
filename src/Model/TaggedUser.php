<?php

namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class TaggedUser
 * 
 * @author Dan Richards <danrichardsri@gmail.com>
 */
class TaggedUser extends Eloquent
{
	
	/** @var string $table */
	protected $table = 'tagging_tagged_user';

	/** @var bool $timestamps */
	public $timestamps = true;

	/** @var array $fillable */
	public $fillable = ['tagged_id', 'user_id'];

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
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user()
	{
		$util = $this->taggingUtility;
		return $this->hasOne($util::userModelString(), 'id', 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function tagged()
	{
		$util = $this->taggingUtility;
		return $this->hasOne($util::taggedModelString(), 'id', 'tagged_id');
	}
	
}