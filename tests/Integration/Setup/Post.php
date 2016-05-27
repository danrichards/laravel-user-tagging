<?php

namespace Dan\Tagging\Testing\Integration\Setup;

use Dan\Tagging\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * A test model for Integration tests.
 *
 * @package Dan\Tagging\Testing\Integration\Models
 */
class Post extends Model
{

    /** @var string $table */
    protected $table = 'posts';

    /** @var array $fillable */
    protected $fillable = ['title', 'body'];

    /** @var bool $timestamps */
    public $timestamps = true;

    use Taggable;

}