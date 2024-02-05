<?php

namespace Pardalsalcap\HailoRedirections\Commands;

use Illuminate\Console\Command;

class HailoRedirectionsCommand extends Command
{
    public $signature = 'hailo-redirections';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
