<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Reader\File;

use Dotenv\Dotenv;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;

class EnvFileReader implements ReaderInterface
{
    /**
     * Load `.env` file in given directory.
     */
    public function read(string $dotEnv): array
    {
        return Dotenv::createMutable(
            dirname($dotEnv),
            basename($dotEnv)
        )->load();
    }
}
