<?php

declare(strict_types=1);

namespace Aranyasen\LaravelEnvSync\Console;

use Aranyasen\LaravelEnvSync\FileNotFound;
use Aranyasen\LaravelEnvSync\Events\MissingEnvVars;
use Aranyasen\LaravelEnvSync\SyncService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

class CheckCommand extends BaseCommand
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:check {--src=} {--dest=} {--reverse}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if your envs files are in sync';

    private SyncService $sync;

    /**
     * Create a new command instance.
     *
     * @param SyncService $sync
     */
    public function __construct(SyncService $sync)
    {
        parent::__construct();
        $this->sync = $sync;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFound
     */
    public function handle(): int
    {
        try {
            [$src, $dest] = $this->getSrcAndDest();
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return Command::FAILURE;
        }

        if ($this->option('reverse')) {
            [$src, $dest] = [$dest, $src];
        }

        $diffs = $this->sync->getDiff($src, $dest);

        if (count($diffs) === 0) {
            $this->info(sprintf("Your %s file is already in sync with %s", basename($dest), basename($src)));
            return self::SUCCESS;
        }

        MissingEnvVars::dispatch($diffs);

        $this->info(sprintf("The following variables are not present in your %s file : ", basename($dest)));
        foreach ($diffs as $key => $diff) {
            $this->info(sprintf("\t- %s = %s", $key, $diff));
        }

        $this->info(sprintf("You can use `php artisan env:sync%s` to synchronise them", $this->option('reverse') ? ' --reverse' : ''));

        return 1;
    }
}
