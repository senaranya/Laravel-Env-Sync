<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Reader;

interface ReaderInterface
{
    public function read(string $dotEnv): array;
}
