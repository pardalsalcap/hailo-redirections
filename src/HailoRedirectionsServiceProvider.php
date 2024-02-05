<?php

namespace Pardalsalcap\HailoRedirections;

use Livewire\Livewire;
use Pardalsalcap\HailoRedirections\Commands\HailoRedirectionsCommand;
use Pardalsalcap\HailoRedirections\Livewire\Redirections\RedirectionsApp;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HailoRedirectionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('hailo-redirections')
            ->hasRoute('hailo-redirections')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_hailo_redirections_table')
            ->hasCommand(HailoRedirectionsCommand::class);
    }

    public function bootingPackage()
    {
        Livewire::component('redirections-app', RedirectionsApp::class);
    }
}
