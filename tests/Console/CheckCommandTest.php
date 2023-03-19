<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\Console\CheckCommand;
use Aranyasen\LaravelEnvSync\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

class CheckCommandTest extends TestCase
{
    /** @test */
    public function it_should_return_0_when_keys_are_in_both_files(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\n" . "BAR=BAZ\n". "BAZ=FOO");
        $this->setEnvFile('.env', "BAR=BAZ\n" . "FOO=BAR\n" . "BAZ=FOO");

        self::assertSame(CheckCommand::SUCCESS, Artisan::call('env:check'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_should_return_1_when_files_are_different(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\n" . "BAR=BAZ\n" . "BAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\n" . "BAZ=FOO");
        self::assertSame(CheckCommand::FAILURE, Artisan::call('env:check'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_should_work_in_reverse_mode(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\n" . "BAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\n" . "BAR=BAZ\n" . "BAZ=FOO");

        self::assertSame(CheckCommand::FAILURE, Artisan::call('env:check', ["--reverse" => true]));
    }

    /** @test */
    public function it_throws_exception_when_one_dotenv_is_provided_but_not_the_other(): void
    {
        $this
            ->artisan('env:check', ['--src' => $this->getFilePath('.foo')])
            ->expectsOutput("You must use either both src and dest options, or none.")
            ->assertExitCode(Command::FAILURE);
    }
}
