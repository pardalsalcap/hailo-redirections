<?php

namespace Pardalsalcap\HailoRedirections\Tests\Feature;

use Pardalsalcap\HailoRedirections\Models\Redirection;

use function Pest\Laravel\get;

it('redirects a non-existent page if a rule exists', function () {
    Redirection::create([
        'url' => '/missing-page',
        'fix' => '/existing-page',
        'http_status' => 301,
        'hash' => md5('/missing-page'),
    ]);

    $response = get('/missing-page');
    $response->assertRedirect('/existing-page');
    $response->assertStatus(301);
})->group('HailoRedirections');
