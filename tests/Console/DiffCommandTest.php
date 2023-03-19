<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

class DiffCommandTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_one_dotenv_is_provided_but_not_the_other(): void
    {
        $this
            ->artisan('env:diff', ['--src' => $this->getFilePath('.foo')])
            ->expectsOutput("You must use either both src and dest options, or none.")
            ->assertExitCode(Command::FAILURE);
    }

    /** @test */
    public function it_should_fill_the_env_file_from_env_example(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAZ=FOO");

        $returnCode = Artisan::call('env:diff');

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
        self::assertSame(Command::FAILURE, $returnCode);
    }
}
