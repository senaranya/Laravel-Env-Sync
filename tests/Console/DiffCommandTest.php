<?php

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\EnvSyncServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;

class DiffCommandTest extends TestCase
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
        $returnCode = Artisan::call('env:diff', []);

        $expected = <<<TAG
+-----+-----------+--------------+
| Key | .env      | .env.example |
+-----+-----------+--------------+
| BAR | NOT FOUND | BAZ          |
| BAZ | FOO       | FOO          |
| FOO | BAR       | BAR          |
+-----+-----------+--------------+

TAG;
        self::assertSame($expected, Artisan::output());
        self::assertSame(1, (int)$returnCode);
    }
}
