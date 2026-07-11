<?php

namespace PavelMironchik\LaravelBackupPanel\Tests\Feature;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PavelMironchik\LaravelBackupPanel\Actions\CreateBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\DeleteBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\DownloadBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\ShowBackupPanelAction;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Jobs\CreateBackupJob;
use PavelMironchik\LaravelBackupPanel\LaravelBackupPanel;
use PavelMironchik\LaravelBackupPanel\Tests\TestCase;

class InterfaceTest extends TestCase
{
    public function test_routes_use_invokable_actions(): void
    {
        $this->assertRouteUses('laravel-backup-panel.index', ShowBackupPanelAction::class);
        $this->assertRouteUses('laravel-backup-panel.backups.store', CreateBackupAction::class);
        $this->assertRouteUses('laravel-backup-panel.backups.download', DownloadBackupAction::class);
        $this->assertRouteUses('laravel-backup-panel.backups.destroy', DeleteBackupAction::class);
    }

    public function test_panel_is_served_at_configured_path(): void
    {
        $this->get('/backup')->assertOk();
    }

    public function test_home_view_is_served(): void
    {
        $this->get('/backup')
            ->assertViewIs('laravel_backup_panel::index')
            ->assertViewHas('activeDisk', null)
            ->assertViewHas('files', static fn (Collection $files): bool => $files->isEmpty());
    }

    public function test_panel_uses_english_regardless_of_application_locale(): void
    {
        app()->setLocale('es');

        $this->get('/backup')
            ->assertSeeText('Laravel Backup Panel')
            ->assertSeeText('Create Backup');
    }

    public function test_creating_backups_queues_each_supported_option(): void
    {
        Queue::fake();

        foreach (BackupMode::cases() as $mode) {
            $this->post('/backup/backups', ['mode' => $mode->value])
                ->assertRedirect('/backup');
        }

        foreach (BackupMode::cases() as $mode) {
            Queue::assertPushed(CreateBackupJob::class, fn (CreateBackupJob $job): bool => $job->mode === $mode);
        }
    }

    public function test_creating_backup_rejects_unknown_option(): void
    {
        $this->from('/backup')
            ->post('/backup/backups', ['mode' => 'everything'])
            ->assertRedirect('/backup')
            ->assertSessionHasErrors('mode');
    }

    public function test_creating_backup_requires_an_explicit_mode(): void
    {
        $this->from('/backup')
            ->post('/backup/backups')
            ->assertRedirect('/backup')
            ->assertSessionHasErrors('mode');
    }

    public function test_backup_can_be_downloaded_and_deleted(): void
    {
        $path = 'test-backups/test-backup.zip';
        Storage::disk('local')->put($path, 'backup contents');

        $this->get("/backup/backups/download?disk=local&path={$path}")
            ->assertOk()
            ->assertDownload('test-backup.zip');

        $this->delete("/backup/backups?disk=local&path={$path}")
            ->assertRedirect('/backup?disk=local');

        Storage::disk('local')->assertMissing($path);
    }

    public function test_download_rejects_unconfigured_disk(): void
    {
        $this->from('/backup')
            ->get('/backup/backups/download?disk=unknown&path=unknown.zip')
            ->assertRedirect('/backup')
            ->assertSessionHasErrors('disk');
    }

    public function test_download_rejects_missing_backup(): void
    {
        $this->get('/backup/backups/download?disk=local&path=test-backups/missing.zip')
            ->assertNotFound();
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        Config::set('app.key', 'base64:GhFMLyZ7x32kzu0How7wF8CIei+UC9Lc69Jcr+Z3sAk=');
        Config::set('backup.backup.name', 'test-backups');
        Config::set('backup.backup.destination.disks', ['local']);
        Config::set('backup.monitor_backups', [[
            'name' => 'test-backups',
            'disks' => ['local'],
            'health_checks' => [],
        ]]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Storage::fake('local');

        LaravelBackupPanel::auth(fn (): bool => true);

        app()->instance('path.public', __DIR__.'/../../public');
    }

    /**
     * @param  class-string  $action
     */
    private function assertRouteUses(string $name, string $action): void
    {
        $route = app('router')->getRoutes()->getByName($name);

        self::assertInstanceOf(Route::class, $route);
        self::assertSame($action.'@__invoke', $route->getAction('uses'));
    }
}
