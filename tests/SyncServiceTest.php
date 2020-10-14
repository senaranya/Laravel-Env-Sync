<?php

namespace Aranyasen\LaravelEnvSync\Tests;

use Aranyasen\LaravelEnvSync\FileNotFound;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use Aranyasen\LaravelEnvSync\SyncService;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class SyncServiceTest extends TestCase
{
    /** @test */
    public function it_should_return_the_difference_between_files(): void
    {
        $root = vfsStream::setup("sync_service");
        $source = $root->url() . '/source';
        $destination = $root->url() . '/dest';
        touch($source);
        touch($destination);


        $readerInterface = \Mockery::mock(ReaderInterface::class)
            ->shouldReceive('read')->twice()->andReturn([
                "foo" => "bar",
                "baz" => "foo",
            ], [
                "foo" => "bar",
                "bar" => "baz",
                "baz" => "foo"
            ]);

        $sync = new SyncService($readerInterface->getMock());

        $result = $sync->getDiff($source, $destination);

        self::assertSame(['bar' => 'baz'], $result);
    }

    /** @test */
    public function it_should_throw_an_exception_if_file_is_not_found(): void
    {
        $root = vfsStream::setup("sync_service_2");
        $source = $root->url() . '/source';
        $destination = $root->url() . '/dest';
        touch($source);

        $this->expectException(FileNotFound::class);
        $this->expectExceptionMessage(sprintf("%s must exists", $destination));

        $sync = new SyncService(\Mockery::mock(ReaderInterface::class));

        $sync->getDiff($source, $destination);

    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
