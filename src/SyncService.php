<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync;

use Aranyasen\LaravelEnvSync\Reader\ReaderInterface;

class SyncService
{
    private ReaderInterface $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $source
     * @param $destination
     * @return array
     * @throws FileNotFound
     */
    public function getDiff($source, $destination): array
    {
        $this->ensureFileExists($source, $destination);

        $destinationValues = $this->reader->read($destination);
        $sourceValues = $this->reader->read($source);

        $diffKeys = array_diff(array_keys($sourceValues), array_keys($destinationValues));

        return array_filter($sourceValues, static fn($key) => in_array($key, $diffKeys, true), ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param mixed ...$files
     * @throws FileNotFound
     */
    private function ensureFileExists(...$files): void
    {
        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new FileNotFound(sprintf("%s must exists", $file));
            }
        }
    }
}
