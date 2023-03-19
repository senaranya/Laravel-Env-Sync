<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Console;

use Illuminate\Console\Command;
use RuntimeException;

class BaseCommand extends Command
{
    protected function getSrcAndDest(): array
    {
        if ($this->option('src') !== null || $this->option('dest') !== null) {
            if ($this->option('src') === null || $this->option('dest') === null) {
                throw new RuntimeException('You must use either both src and dest options, or none.');
            }
        }

        return [
            $this->option('src') ?: base_path('.env.example'),
            $this->option('dest') ?: base_path('.env')
        ];
    }
}
