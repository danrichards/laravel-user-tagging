<?php

namespace Dan\Tagging\Testing\Integration\Setup\Users;

use App\User;
use Dan\Tagging\Repositories\Users\UsersInterface as BaseUsersInterface;
use Dan\Tagging\Repositories\Users\UsersRepository as BaseUsersRepository;

class UsersRepository extends BaseUsersRepository implements BaseUsersInterface
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = \Dan\Tagging\Testing\Integration\Setup\User::class;
    
}