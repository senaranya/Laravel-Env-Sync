<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests;

use Aranyasen\LaravelEnvSync\FileNotFound;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use Aranyasen\LaravelEnvSync\SyncService;

class SyncServiceTest extends TestCase
{
    /** @test */
    public function it_should_return_the_difference_between_files(): void
    {
        $this->setEnvFile('source', '');
        $this->setEnvFile('dest', '');

        $readerInterface = $this->mock(ReaderInterface::class)
            ->shouldReceive('read')->twice()->andReturn([
                "foo" => "bar",
                "baz" => "foo",
            ], [
                "foo" => "bar",
                "bar" => "baz",
                "baz" => "foo"
            ]);

        $sync = new SyncService($readerInterface->getMock());

        self::assertSame(
            ['bar' => 'baz'],
            $sync->getDiff($this->getFilePath('source'), $this->getFilePath('dest'))
        );
    }

    /** @test */
    public function it_should_throw_an_exception_if_file_is_not_found(): void
    {
        $this->setEnvFile('source', '');
        $destination = $this->getFilePath('dest');
        $this->expectException(FileNotFound::class);
        $this->expectExceptionMessage(sprintf("%s must exists", $destination));

        $sync = new SyncService($this->mock(ReaderInterface::class));

        $sync->getDiff($this->getFilePath('source'), $destination);

    }
}
