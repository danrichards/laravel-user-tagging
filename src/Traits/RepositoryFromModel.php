<?php

namespace Dan\Tagging\Traits;

use Torann\LaravelRepository\RepositoryFactory;

/**
 * Trait Repository
 *
 * @self \Illuminate\Database\Eloquent\Model
 */
trait RepositoryFromModel
{

    /**
     * @var \Torann\LaravelRepository\Repositories\AbstractRepository
     */
    protected $repository;

    /**
     * Get a repository from a Model
     *
     * @param bool $withCache
     * @return \Torann\LaravelRepository\Repositories\RepositoryInterface
     * @throws \Exception
     */
    public function getRepository($withCache = true)
    {
        if (is_null($this->repository)) {
            $name = array_last(explode('\\', __CLASS__));
            return $withCache
                ? $this->repository = RepositoryFactory::createWithCache(str_plural($name))
                : $this->repository = RepositoryFactory::create(str_plural($name));
        } else {
            return $this->repository;
        }
    }

}