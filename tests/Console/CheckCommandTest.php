<?php

namespace Aranyasen\LaravelEnvSync\Tests\Console;

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

    protected function tearDown(): void
    {
        @unlink(vfsStream::setup()->url() . '/.env');
        @unlink(vfsStream::setup()->url() . '/.env.example');

        parent::tearDown();
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
        self::assertSame(0, Artisan::call('env:check'));
    }

    /** @test */
    public function it_should_return_1_when_files_are_different(): void
    {
        $root = vfsStream::setup();
        $example = "FOO=BAR" . PHP_EOL . "BAR=BAZ". PHP_EOL . "BAZ=FOO";
        $env = "FOO=BAR" . PHP_EOL . "BAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);
        $this->app->setBasePath($root->url());
        self::assertSame(1, Artisan::call('env:check'));
    }

    /** @test */
    public function it_should_work_in_reverse_mode(): void
    {
        $root = vfsStream::setup();
        $env = "FOO=BAR" . PHP_EOL . "BAR=BAZ" . PHP_EOL . "BAZ=FOO";
        $example = "FOO=BAR" . PHP_EOL . "BAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);

        $this->app->setBasePath($root->url());
        self::assertSame(1, Artisan::call('env:check', ["--reverse" => true]));
    }
}
