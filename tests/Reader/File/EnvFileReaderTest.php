<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Tests\Reader\File;

use Aranyasen\LaravelEnvSync\Reader\File\EnvFileReader;
use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use Aranyasen\LaravelEnvSync\Tests\TestCase;

class EnvFileReaderTest extends TestCase
{
    private ReaderInterface $reader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reader = new EnvFileReader();
    }

    /** @test
     */
    public function it_should_return_array_from_file_content(): void
    {
        $this->setEnvFile('.env', <<<TAG
            APP_SECRET=FOO
            APP_TEST=BAR
            # COMMENT
            
            TEST=ZOO
            TAG
        );
        self::assertSame(
            [
                'APP_SECRET' => 'FOO',
                'APP_TEST' => 'BAR',
                'TEST' => 'ZOO',
            ],
            $this->reader->read($this->getFilePath('.env'))
        );
    }
}
