<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests\Console;

use Aranyasen\LaravelEnvSync\FileNotFound;
use Aranyasen\LaravelEnvSync\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

class SyncCommandTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_one_dotenv_is_provided_but_not_the_other(): void
    {
        $this
            ->artisan('env:sync', ['--src' => $this->getFilePath('.foo')])
            ->expectsOutput("You must use either both src and dest options, or none.")
            ->assertExitCode(Command::FAILURE);
    }

    /** @test */
    public function it_should_fill_the_env_file_from_env_example(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAZ=FOO");

        Artisan::call('env:sync', [
            '--no-interaction' => true,
        ]);

        self::assertSame("FOO=BAR\nBAZ=FOO" . PHP_EOL . "BAR=BAZ", $this->getDotEnvContents('.env'));
    }

    /** @test */
    public function it_should_work_in_reverse_mode(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");

        Artisan::call('env:sync', [
            '--no-interaction' => true,
            '--reverse' => true,
        ]);

        self::assertSame("FOO=BAR\nBAZ=FOO" . PHP_EOL . "BAR=BAZ", $this->getDotEnvContents('.env.example'));
    }

    /** @test */
    public function it_should_work_when_providing_src_and_dest(): void
    {
        $this->setEnvFile('.foo', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.bar', "FOO=BAR\nBAZ=FOO");

        Artisan::call('env:sync', [
            '--no-interaction' => true,
            '--src' => $this->getFilePath('.foo'),
            '--dest' => $this->getFilePath('.bar')
        ]);

        self::assertSame("FOO=BAR\nBAZ=FOO" . PHP_EOL . "BAR=BAZ", $this->getDotEnvContents('.bar'));
    }

    /** @test
     * @throws FileNotFound
     */
    public function in_interactive_mode_user_says_sync_it(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAZ=FOO");

        $this
            ->artisan('env:sync')
            ->expectsQuestion("'BAR' is not present into your .env file. Its default value is 'BAZ'. Would you like to add it?", 'y');
        self::assertSame("FOO=BAR\nBAZ=FOO" . PHP_EOL . "BAR=BAZ", $this->getDotEnvContents('.env'));
    }

    /** @test
     * @throws FileNotFound
     */
    public function in_interactive_mode_user_says_dont_sync_it(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAZ=FOO");

        $this
            ->artisan('env:sync')
            ->expectsQuestion("'BAR' is not present into your .env file. Its default value is 'BAZ'. Would you like to add it?", 'n');
        self::assertSame("FOO=BAR\nBAZ=FOO", $this->getDotEnvContents('.env'));
    }

    /** @test
     * @throws FileNotFound
     */
    public function in_interactive_mode_user_says_change_the_value_before_syncing(): void
    {
        $this->setEnvFile('.env.example', "FOO=BAR\nBAR=BAZ\nBAZ=FOO");
        $this->setEnvFile('.env', "FOO=BAR\nBAZ=FOO");

        $this
            ->artisan('env:sync')
            ->expectsQuestion("'BAR' is not present into your .env file. Its default value is 'BAZ'. Would you like to add it?", 'c')
            ->expectsQuestion("Please choose a value for 'BAR'", "some_value");
        self::assertSame("FOO=BAR\nBAZ=FOO" . PHP_EOL . "BAR=some_value", $this->getDotEnvContents('.env'));
    }
}
