<?php

namespace Trungdev05\LaravelBackupPanel\Tests\Feature;

use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Trungdev05\LaravelBackupPanel\LaravelBackupPanel;
use Trungdev05\LaravelBackupPanel\LaravelBackupPanelApplicationServiceProvider;
use Trungdev05\LaravelBackupPanel\Tests\TestCase;

class ApplicationAuthorizationTest extends TestCase
{
    public function test_application_provider_denies_access_by_default_outside_local(): void
    {
        self::assertFalse(LaravelBackupPanel::check(Request::create('/backup')));
    }

    public function test_application_provider_allows_access_in_the_local_environment(): void
    {
        app()->detectEnvironment(static fn (): string => 'local');

        (new LaravelBackupPanelApplicationServiceProvider(app()))->boot();

        self::assertTrue(LaravelBackupPanel::check(Request::create('/backup')));
    }

    public function test_application_provider_uses_the_configured_gate_for_an_authenticated_user(): void
    {
        $user = new GenericUser(['id' => 1]);
        $this->actingAs($user);

        Gate::define(
            'viewLaravelBackupPanel',
            static fn (GenericUser $authenticatedUser): bool => $authenticatedUser->getAuthIdentifier() === 1,
        );

        self::assertTrue(LaravelBackupPanel::check(Request::create('/backup')));
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            LaravelBackupPanelApplicationServiceProvider::class,
        ];
    }
}
