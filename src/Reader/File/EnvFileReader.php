<?php

namespace Aranyasen\LaravelEnvSync\Reader\File;

use Dotenv\Dotenv;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;

class EnvFileReader implements ReaderInterface
{
    /**
     * Load `.env` file in given directory.
     *
     * @param null $resource
     * @return array
     * @throws FileRequired
     */
    public function read($resource = null)
    {
        if ($resource === null) {
            throw new FileRequired();
        }

        $dir = dirname($resource);
        $name = basename($resource);

        return Dotenv::createImmutable($dir, $name)->load();
    }
}
