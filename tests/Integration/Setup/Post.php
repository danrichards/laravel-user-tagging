<?php

namespace Dan\Tagging\Testing\Integration\Setup;

use Illuminate\Database\Eloquent\Model;
use Dan\Tagging\Taggable;

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