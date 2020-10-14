<?php

namespace Aranyasen\LaravelEnvSync\Tests\Writer\File;

use Aranyasen\LaravelEnvSync\Writer\File\EnvFileWriter;
use Aranyasen\LaravelEnvSync\Writer\WriterInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Throwable;

class EnvFileWriterTest extends TestCase
{
    private vfsStreamDirectory $fs;
    private WriterInterface $writer;

    protected function setUp(): void
    {
        $this->writer = new EnvFileWriter();
        $this->fs = vfsStream::setup("write_env");
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

        $filePath = $this->fs->url() . '/.env';

        $lines = [
            'test=foo',
            'foo=baz',
        ];
        file_put_contents($filePath, implode(PHP_EOL, $lines));
        $this->writer->append($filePath, 'phpunit', 'rocks hard');

        $lines = file($filePath);

        self::assertSame([
            "test=foo" . PHP_EOL,
            "foo=baz" . PHP_EOL,
            "phpunit=\"rocks hard\""
        ], $lines);
    }
}
