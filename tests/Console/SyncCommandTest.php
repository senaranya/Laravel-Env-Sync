<?php

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\EnvSyncServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;

class SyncCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [EnvSyncServiceProvider::class];
    }

    /** @test */
    public function it_should_fill_the_env_file_from_env_example(): void
    {
        $root = vfsStream::setup();
        $example = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);

        $this->app->setBasePath($root->url());
        Artisan::call('env:sync', [
            '--no-interaction' => true,
        ]);

        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        self::assertSame($expected, file_get_contents($root->url() . '/.env'));
    }

    /** @test */
    public function it_should_work_in_reverse_mode(): void
    {
        $root = vfsStream::setup();
        $env= "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $example  = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . '/.env.example', $example);
        file_put_contents($root->url() . '/.env', $env);

        $this->app->setBasePath($root->url());
        Artisan::call('env:sync', [
            '--no-interaction' => true,
            '--reverse' => true,
        ]);

        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        self::assertSame($expected, file_get_contents($root->url() . '/.env.example'));
    }

    /** @test */
    public function it_should_work_when_providing_src_and_dest(): void
    {
        $root = vfsStream::setup();
        $example = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . '/.foo', $example);
        file_put_contents($root->url() . '/.bar', $env);

        $this->app->setBasePath($root->url());
        Artisan::call('env:sync', [
            '--no-interaction' => true,
            '--src' => $root->url() .'/.foo',
            '--dest' => $root->url() .'/.bar'
        ]);

        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        self::assertSame($expected, file_get_contents($root->url() . '/.bar'));
    }
}
