<?php

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\Console\CheckCommand;
use Aranyasen\LaravelEnvSync\EnvSyncServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;

class CheckCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [EnvSyncServiceProvider::class];
    }

    /** @test */
    public function it_should_return_0_when_keys_are_in_both_files(): void
    {
        $root = vfsStream::setup();
        $example = "FOO=BAR" . PHP_EOL . "BAR=BAZ". PHP_EOL . "BAZ=FOO";
        $env = "BAR=BAZ" . PHP_EOL . "FOO=BAR" . PHP_EOL . "BAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);

        $this->app->setBasePath($root->url());
        self::assertSame(CheckCommand::SUCCESS, Artisan::call('env:check'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_should_return_1_when_files_are_different(): void
    {
        $root = vfsStream::setup();
        $example = "FOO=BAR" . PHP_EOL . "BAR=BAZ". PHP_EOL . "BAZ=FOO";
        $env = "FOO=BAR" . PHP_EOL . "BAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);
        $this->app->setBasePath($root->url());
        self::assertSame(CheckCommand::FAILURE, Artisan::call('env:check'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_should_work_in_reverse_mode(): void
    {
        $root = vfsStream::setup();
        $env = "FOO=BAR" . PHP_EOL . "BAR=BAZ" . PHP_EOL . "BAZ=FOO";
        $example = "FOO=BAR" . PHP_EOL . "BAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);

        $this->app->setBasePath($root->url());
        self::assertSame(CheckCommand::FAILURE, Artisan::call('env:check', ["--reverse" => true]));
    }
}
