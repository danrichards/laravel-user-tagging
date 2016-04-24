<?php

namespace Dan\Tagging;

use Torann\LaravelRepository\Cache\LaravelCache;
use Exception;

class RepositoryFactory
{
    /**
     * Create a new repository instance.
     *
     * @param string $name
     * @param null $namespace
     * @return \Torann\LaravelRepository\Repositories\RepositoryInterface ;
     * @throws Exception
     */
    public static function create($name, $namespace = null)
    {
        $namespace = $namespace ?: config('repositories.namespace', '\\App\\Repositories');
        $namespace .= "\\{$name}";
        $class = "{$namespace}\\{$name}Repository";

        // Ensure repository exists
        if (class_exists($class) === false) {
            throw new Exception("Repository \"{$class}\" not found");
        }

        // Create repository instance
        return new $class();
    }

    /**
     * Create a new repository instance with cache decorator.
     *
     * @param  string $name
     * @param null $namespace
     * @param  array $tags
     * @return \Torann\LaravelRepository\Repositories\RepositoryInterface ;
     * @throws Exception
     */
    public static function createWithCache($name, $namespace = null, array $tags = [])
    {
        // Create repository instance
        $repository = self::create($name, $namespace);

        // Globally turned off
        if (config('repositories.cache.enabled', false) === false) {
            return $repository;
        }

        // Check cache system support
        if (in_array(config('cache.default'), ['file', 'database']) === true) {
            throw new Exception("Default cache system does not support tags.");
        }

        // Get cache decorator class
        $namespace = $namespace ?: config('repositories.namespace', '\\App\\Repositories');
        $class = "{$namespace}\\{$name}\\CacheDecorator";

        // Ensure cache decorator exists
        if (class_exists($class) === false) {
            throw new Exception("Repository cache decorator \"{$class}\" not found");
        }

        // Ensure there are tags
        $tags = empty($tags) ? [strtolower($name)] : $tags;

        // Create cache decorator
        return new $class($repository, new LaravelCache($tags));
    }

}