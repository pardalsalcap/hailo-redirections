<?php

namespace Pardalsalcap\HailoRedirections\Actions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use Pardalsalcap\Hailo\Repositories\RoleRepository;
use Pardalsalcap\HailoRedirections\Repositories\RedirectionRepository;

class DestroyRedirection
{
    use AsAction;

    /**
     * @throws Exception
     */
    public function handle(int $redirection_id): bool
    {
        return (new RedirectionRepository())->destroy($redirection_id);
    }
}
