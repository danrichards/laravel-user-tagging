<?php

namespace Dan\Tagging\Repositories;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelRepository\Repositories\AbstractCacheDecorator as BaseAbstractCacheDecorator;

/**
 * Class AbstractCacheDecorator
 */
class AbstractCacheDecorator extends BaseAbstractCacheDecorator
{

    /**
     * Get Cache key for the method
     *
     * @see https://github.com/Torann/laravel-repository/pull/3
     * @param  string $method
     * @param  mixed  $args
     * @return string
     */
    public function getCacheKey($method, $args = null)
    {
        foreach($args as &$a) {
            if ($a instanceof Model) {
                $a = get_class($a).'|'.$a->getKey();
            }
        }

        $args = serialize($args)
            . serialize($this->repo->getScopeQuery())
            . serialize($this->repo->getWith());

        return sprintf('%s-%s@%s-%s',
            config('app.locale'),
            get_called_class(),
            $method,
            md5($args)
        );
    }
}