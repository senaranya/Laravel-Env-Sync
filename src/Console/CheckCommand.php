<?php

namespace Aranyasen\LaravelEnvSync\Console;

use Aranyasen\LaravelEnvSync\FileNotFound;
use Aranyasen\LaravelEnvSync\Events\MissingEnvVars;
use Aranyasen\LaravelEnvSync\SyncService;

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
        [$src, $dest] = $this->getSrcAndDest();

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
