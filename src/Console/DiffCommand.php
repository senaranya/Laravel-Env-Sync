<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Console;

use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

class DiffCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:diff {--src=} {--dest=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the difference between env files';

    private ReaderInterface $reader;

    private int $returnCode = Command::SUCCESS;

    /**
     * Create a new command instance.
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        parent::__construct();
        $this->reader = $reader;
    }

    public function handle(): int
    {
        try {
            [$src, $dest] = $this->getSrcAndDest();
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return Command::FAILURE;
        }

        $envValues = $this->reader->read($dest);
        $exampleValues = $this->reader->read($src);

        $keys = array_unique(array_merge(array_keys($envValues), array_keys($exampleValues)));
        sort($keys);

        $header = ["Key", basename($dest), basename($src)];
        $lines = [];
        foreach ($keys as $key) {
            $lines[] = [
                $key,
                $envValues[$key] ?? $this->errorText(),
                $exampleValues[$key] ?? $this->errorText()
            ];
        }

        $this->table($header, $lines);

        return $this->returnCode;
    }

    private function errorText(): string
    {
        $this->returnCode = Command::FAILURE;
        return '<error>NOT FOUND</error>';
    }
}
