<?php

namespace Dan\Tagging\Repositories\TaggedUser;

use Dan\Tagging\Models\TaggedUser;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Torann\LaravelRepository\Repositories\AbstractRepository;

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