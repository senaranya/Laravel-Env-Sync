<?php

namespace Aranyasen\LaravelEnvSync\Events;

use Illuminate\Foundation\Events\Dispatchable;

class MissingEnvVars
{
    use Dispatchable;

    public $diffs;

    public function __construct($diffs)
    {
        $this->diffs = $diffs;
    }
}
