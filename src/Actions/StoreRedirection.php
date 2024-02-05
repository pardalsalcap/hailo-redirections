<?php

namespace Pardalsalcap\HailoRedirections\Actions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use Pardalsalcap\Hailo\Repositories\RoleRepository;
use Pardalsalcap\HailoRedirections\Models\Redirection;
use Pardalsalcap\HailoRedirections\Repositories\RedirectionRepository;
use Spatie\Permission\Models\Role;

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
