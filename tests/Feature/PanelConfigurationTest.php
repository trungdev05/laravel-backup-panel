<?php

namespace PavelMironchik\LaravelBackupPanel\Tests\Feature;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use PavelMironchik\LaravelBackupPanel\Support\PanelConfiguration;
use PavelMironchik\LaravelBackupPanel\Tests\TestCase;

class PanelConfigurationTest extends TestCase
{
    public function test_path_must_be_non_empty(): void
    {
        Config::set('laravel_backup_panel.path', '');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('laravel_backup_panel.path');

        app(PanelConfiguration::class)->path();
    }

    public function test_queue_must_use_an_asynchronous_driver(): void
    {
        Config::set('queue.default', 'null');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('driver other than [sync] or [null]');

        app(PanelConfiguration::class)->queue();
    }

    public function test_queue_name_must_be_non_empty(): void
    {
        Config::set('laravel_backup_panel.queue', '');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('laravel_backup_panel.queue');

        app(PanelConfiguration::class)->queue();
    }

    public function test_middleware_must_be_a_list(): void
    {
        Config::set('laravel_backup_panel.middleware', ['auth' => 'auth']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a list of middleware strings');

        app(PanelConfiguration::class)->middleware();
    }

    public function test_middleware_must_contain_only_non_empty_strings(): void
    {
        Config::set('laravel_backup_panel.middleware', ['auth', ['can:access-backup-panel']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('only non-empty middleware strings');

        app(PanelConfiguration::class)->middleware();
    }
}
