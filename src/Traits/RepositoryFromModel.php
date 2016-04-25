<?php

namespace Dan\Tagging\Traits;

use Dan\Tagging\RepositoryFactory;

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
    public function getRepository($withCache = null)
    {
        $withCache = is_null($withCache) ? config('repositories.cache.enabled', false) : $withCache;
        if (is_null($this->repository)) {
            $namespace = explode('\\', __CLASS__);
            $name = str_plural(array_pop($namespace));
            array_pop($namespace); // lose the 'Models'
            array_push($namespace, 'Repositories');
            $namespace = implode('\\', $namespace);
            return $withCache
                ? $this->repository = RepositoryFactory::createWithCache($name, $namespace)
                : $this->repository = RepositoryFactory::create($name, $namespace);
        } else {
            return $this->repository;
        }
    }

}