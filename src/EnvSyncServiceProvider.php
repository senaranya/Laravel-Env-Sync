<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync;

use Illuminate\Support\ServiceProvider;
use Aranyasen\LaravelEnvSync\Reader\File\EnvFileReader;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use Aranyasen\LaravelEnvSync\Writer\File\EnvFileWriter;
use Aranyasen\LaravelEnvSync\Writer\WriterInterface;

class EnvSyncServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // bindings
        $this->app->bind(ReaderInterface::class, EnvFileReader::class);
        $this->app->bind(WriterInterface::class, EnvFileWriter::class);

        // artisan command
        $this->commands(Console\SyncCommand::class);
        $this->commands(Console\CheckCommand::class);
        $this->commands(Console\DiffCommand::class);
    }

    public function provides(): array
    {
        return [
            ReaderInterface::class,
            WriterInterface::class,
        ];
    }
}
