<?php

namespace PavelMironchik\LaravelBackupPanel\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PavelMironchik\LaravelBackupPanel\Http\Middleware\Authenticate;
use PavelMironchik\LaravelBackupPanel\LaravelBackupPanel;
use PavelMironchik\LaravelBackupPanel\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthTest extends TestCase
{
    public function test_authentication_callback_works(): void
    {
        $adminRequest = Request::create('/backup');
        $hackerRequest = Request::create('/other');

        self::assertFalse(LaravelBackupPanel::check($adminRequest));

        LaravelBackupPanel::auth(static function (Request $request): bool {
            return $request->path() === 'backup';
        });

        self::assertTrue(LaravelBackupPanel::check($adminRequest));
        self::assertFalse(LaravelBackupPanel::check($hackerRequest));
    }

    public function test_authentication_middleware_can_pass(): void
    {
        LaravelBackupPanel::auth(static fn (Request $request): bool => true);

        $middleware = new Authenticate();
        $request = Request::create('/backup');
        $expectedResponse = new Response('response');

        $response = $middleware->handle(
            $request,
            static fn (Request $request): Response => $expectedResponse,
        );

        self::assertSame($expectedResponse, $response);
    }

    public function test_authentication_middleware_responds_with_403_on_failure(): void
    {
        $this->expectException(HttpException::class);

        LaravelBackupPanel::auth(static fn (Request $request): bool => false);

        $middleware = new Authenticate();

        $middleware->handle(
            Request::create('/backup'),
            static fn (Request $request): Response => new Response('response'),
        );
    }
}
