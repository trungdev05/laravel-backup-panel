<?php

namespace Trungdev05\LaravelBackupPanel\Tests\Feature;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Trungdev05\LaravelBackupPanel\Actions\CreateBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\DeleteBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\DownloadBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\ShowBackupPanelAction;
use Trungdev05\LaravelBackupPanel\Enums\BackupMode;
use Trungdev05\LaravelBackupPanel\Jobs\CreateBackupJob;
use Trungdev05\LaravelBackupPanel\LaravelBackupPanel;
use Trungdev05\LaravelBackupPanel\Tests\TestCase;

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
            ->assertViewHas('activeDisk', 'local')
            ->assertViewHas('backupStatuses', static fn (Collection $statuses): bool => $statuses->count() === 1)
            ->assertViewHas('files', static fn (Collection $files): bool => $files->isEmpty());
    }

    public function test_panel_uses_english_regardless_of_application_locale(): void
    {
        app()->setLocale('es');

        $this->get('/backup')
            ->assertSeeText('Laravel Backup Panel')
            ->assertSeeText('Create Backup');
    }

    #[DataProvider('backupModes')]
    public function test_creating_backups_queues_each_supported_option(BackupMode $mode): void
    {
        Queue::fake();

        $this->post('/backup/backups', ['mode' => $mode->value])
            ->assertRedirect('/backup');

        Queue::assertPushed(
            CreateBackupJob::class,
            fn (CreateBackupJob $job): bool => $job->mode === $mode,
        );
    }

    /** @return array<string, array{BackupMode}> */
    public static function backupModes(): array
    {
        return [
            'full' => [BackupMode::Full],
            'database only' => [BackupMode::OnlyDatabase],
            'files only' => [BackupMode::OnlyFiles],
        ];
    }

    public function test_creating_a_backup_deduplicates_concurrent_requests(): void
    {
        Queue::fake();

        $this->post('/backup/backups', ['mode' => BackupMode::Full->value])
            ->assertRedirect('/backup');

        $this->post('/backup/backups', ['mode' => BackupMode::OnlyFiles->value])
            ->assertRedirect('/backup');

        Queue::assertPushedTimes(CreateBackupJob::class, 1);
    }

    public function test_creating_a_backup_rejects_a_synchronous_queue_connection(): void
    {
        Config::set('queue.default', 'sync');

        $this->withoutExceptionHandling();
        $this->expectException(InvalidArgumentException::class);

        $this->post('/backup/backups', ['mode' => BackupMode::Full->value]);
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
        Config::set('queue.default', 'database');
        Config::set('backup.backup.name', 'test-backups');
        Config::set('backup.backup.destination.disks', ['local']);
        Config::set('backup.monitor_backups', [[
            'name' => 'test-backups',
            'disks' => ['local'],
            'health_checks' => [],
        ], [
            'name' => 'other-application',
            'disks' => ['other'],
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
