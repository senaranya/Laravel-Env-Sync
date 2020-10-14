<?php

namespace Aranyasen\LaravelEnvSync\Console;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    /**
     * @return array
     */
    public function getSrcAndDest(): array
    {
        if ($this->option('src') !== null || $this->option('dest') !== null) {
            if ($this->option('src') === null || $this->option('dest') === null) {
                $this->error("You must use either both src and dest options, or none.");
                exit(1);
            }
        }

        return [
            $this->option('src') ?: base_path('.env.example'),
            $this->option('dest') ?: base_path('.env')
        ];
    }
}
