<?php

namespace Dan\Tagging\Repositories\TaggedUser;

use Torann\LaravelRepository\Repositories\AbstractRepository;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Dan\Tagging\Models\TaggedUser;

/**
 * Class TaggedUserRepository
 */
class TaggedUserRepository extends AbstractRepository implements TaggedUserInterface
{

    use TaggingUtility;

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = TaggedUser::class;

}