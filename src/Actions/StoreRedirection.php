<?php

namespace Pardalsalcap\HailoRedirections\Actions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use Pardalsalcap\HailoRedirections\Repositories\RedirectionRepository;

class StoreRedirection
{
    use AsAction;

    /**
     * @throws Exception
     */
    public function handle(array $values): void
    {
        (new RedirectionRepository())->store($values);
    }
}
