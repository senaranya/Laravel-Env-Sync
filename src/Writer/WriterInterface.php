<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Writer;

interface WriterInterface
{
    public function append(string $dotEnvFile, $key, $value): void;
}
