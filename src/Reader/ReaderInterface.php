<?php

namespace Aranyasen\LaravelEnvSync\Reader;

interface ReaderInterface
{
    /**
     * Return the content of an env file as an array
     *
     * @param string|null $resource resource where is located the env content
     *
     * @return array
     */
    public function read($resource = null);
}
