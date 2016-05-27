<?php

namespace Dan\Tagging\Testing\Integration\Setup\Posts;

use Dan\Tagging\Testing\Integration\Setup\Post;
use Dan\Tagging\Repositories\Taggable\AbstractTaggableRepository;

/**
 * Class PostsRepository
 */
class PostsRepository extends AbstractTaggableRepository implements PostsInterface
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Post::class;
    
}