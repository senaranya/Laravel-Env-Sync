<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests\Writer\File;

use Aranyasen\LaravelEnvSync\Tests\TestCase;
use Aranyasen\LaravelEnvSync\Writer\File\EnvFileWriter;
use Aranyasen\LaravelEnvSync\Writer\WriterInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Throwable;

class EnvFileWriterTest extends TestCase
{
    private WriterInterface $writer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->writer = new EnvFileWriter();
    }

    /** @test */
    public function it_should_append_content_to_file(): void
    {
        app()->instance(
            ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(Throwable $e) {}
            public function render($request, Throwable $e)
            {
                echo $e->getMessage();
                throw $e;
            }
        });

        $filePath = $this->getFilePath('.env');

        $this->setEnvFile('.env', "test=foo" . PHP_EOL . "foo=baz");
        $this->writer->append($filePath, 'phpunit', 'rocks hard');

        self::assertSame(
            [
                "test=foo" . PHP_EOL,
                "foo=baz" . PHP_EOL,
                "phpunit=\"rocks hard\"",
            ],
            file($filePath)
        );
    }
}
