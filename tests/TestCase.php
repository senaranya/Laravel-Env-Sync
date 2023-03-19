<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests;

use Aranyasen\LaravelEnvSync\EnvSyncServiceProvider;
use Aranyasen\LaravelEnvSync\FileNotFound;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected vfsStreamDirectory $rootFileSystem;

    protected function getPackageProviders($app): array
    {
        return [EnvSyncServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->rootFileSystem = vfsStream::setup();
        $this->app->setBasePath($this->rootFileSystem->url());
    }

    protected function setEnvFile(string $envFile, string $content): void
    {
        file_put_contents($this->rootFileSystem->url() . "/$envFile", $content);
    }

    protected function getFilePath(string $dotEnv): string
    {
        return $this->rootFileSystem->url() . "/$dotEnv";
    }

    protected function getDotEnvContents(string $dotEnv): string
    {
        $filePath = $this->getFilePath($dotEnv);
        if (!file_exists($filePath)) {
            throw new FileNotFound("$dotEnv not present");
        }
        return file_get_contents($filePath);
    }
}
