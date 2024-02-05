<?php

namespace Pardalsalcap\HailoRedirections\Actions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use Pardalsalcap\HailoRedirections\Models\Redirection;
use Pardalsalcap\HailoRedirections\Repositories\RedirectionRepository;

class UpdateRedirection
{
    use AsAction;

    /**
     * @throws Exception
     */
    public function handle(array $values, Redirection $redirection): void
    {
        (new RedirectionRepository())->update($values, $redirection);
    }
}
