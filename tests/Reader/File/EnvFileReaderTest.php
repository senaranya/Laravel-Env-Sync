<?php

namespace Aranyasen\LaravelEnvSync\Tests\Reader\File;

use Aranyasen\LaravelEnvSync\Reader\File\EnvFileReader;
use Aranyasen\LaravelEnvSync\Reader\File\FileRequired;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class EnvFileReaderTest extends TestCase
{
    private vfsStreamDirectory $fs;

    private ReaderInterface $reader;

    protected function setUp(): void
    {
        $this->reader = new EnvFileReader();
        $this->fs = vfsStream::setup("read_env");
    }

    /** @test
     * @throws FileRequired
     */
    public function it_should_return_array_from_file_content(): void
    {
        $filePath = $this->fs->url() . '/.env';

        file_put_contents($filePath, <<<TAG
APP_SECRET=FOO
APP_TEST=BAR
# COMMENT

TEST=ZOO
TAG
        );
        $result = $this->reader->read($filePath);

        self::assertSame([
            'APP_SECRET' => 'FOO',
            'APP_TEST' => 'BAR',
            'TEST' => 'ZOO',
        ], $result);
    }

    /** @test */
    public function it_should_throw_exception_when_no_file_passed(): void
    {
        $this->expectException(FileRequired::class);
        $this->reader->read();
    }
}
