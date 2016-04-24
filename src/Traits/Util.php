<?php

namespace Dan\Tagging\Traits;

use Dan\Tagging\Contracts\TaggingUtility;

/**
 * Trait Util
 */
trait Util
{

    /**
     * @var TaggingUtility $util
     */
    protected $taggingUtility;

    /**
     * @return TaggingUtility
     */
    public function tagUtil()
    {
        return is_null($this->taggingUtility)
            ? $this->taggingUtility = app(TaggingUtility::class)
            : $this->taggingUtility;
    }

}