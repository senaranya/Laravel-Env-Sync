<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Writer\File;

use Aranyasen\LaravelEnvSync\Writer\WriterInterface;

class EnvFileWriter implements WriterInterface
{
    public function append(string $dotEnvFile, $key, $value): void
    {
        $lastChar = substr(file_get_contents($dotEnvFile), -1);

        $prefix = "";
        if ($lastChar !== "\n" && $lastChar !== "\r" && strlen($lastChar) === 1) {
            $prefix = PHP_EOL;
        }

        if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
            $value = '"' . $value . '"';
        }

        file_put_contents($dotEnvFile, $prefix . $key . '=' . $value, FILE_APPEND);
    }
}
