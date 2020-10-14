<?php

namespace Aranyasen\LaravelEnvSync\Writer;


interface WriterInterface
{
    /**
     * Append a new par of key/value to an env resource
     *
     * @param string|null $resource resource where is located the env content
     */
    public function append($resource, $key, $value);
}
