<?php

namespace Dan\Tagging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Dan\Tagging\Traits\RepositoryFromModel;
use Dan\Tagging\Traits\Util as TaggingUtility;

/**
 * Class Tag
 *
 * Copyright (C) 2014 Robert Conner
 */
class Tag extends Model
{

	use RepositoryFromModel, TaggingUtility;
	
	/** @var string $table */
	protected $table = 'tagging_tags';
	
	/** @var bool $timestamps */
	public $timestamps = false;
	
	/** @var bool $softDelete */
	protected $softDelete = false;
	
	/** @var array $fillable */
	public $fillable = ['name'];

	/**
	 * Update the count on the Tag model.
	 *
	 * @return $this
	 */
	public function recalculate()
	{
		$this->count = $this->tagUtil()->taggedModel()
			->where('tag_slug', $this->slug)
			->where('users_count', '>', 0)
			->count();
		$this->save();
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Illuminate\Database\Eloquent\Model::save()
	 * @param array $options
	 * @return bool
	 * @throws \Exception
	 */
	public function save(array $options = [])
	{
		/** @var \Illuminate\Validation\Validator $validator */
		$validator = Validator::make(
			array('name' => $this->name),
			array('name' => 'required|min:1')
		);

		if($validator->passes()) {
			$this->slug = $this->tagUtil()->slug($this->name);
			return parent::save($options);
		} else {
			throw new \Exception('Tag Name is required');
		}
	}

	/**
	 * Set the name of the tag : $tag->name = 'myname';
	 *
	 * @param string $value
	 */
	public function setNameAttribute($value)
	{
		$this->attributes['name'] = $this->tagUtil()->title($value);
	}
	
}
