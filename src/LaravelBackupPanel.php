<?php

namespace PavelMironchik\LaravelBackupPanel;

use Closure;
use Illuminate\Http\Request;

final class LaravelBackupPanel
{
    /**
     * The callback that should be used to authenticate Laravel Backup Panel users.
     *
     * @var Closure(Request): bool
     */
    public static Closure $authUsing;

    /**
     * Determine if the given request can access the Laravel Backup Panel dashboard.
     *
     */
    public static function check(Request $request): bool
    {
        return (self::$authUsing)($request);
    }

    /**
     * @param Closure(Request): bool $callback
     */
    public static function auth(Closure $callback): void
    {
        self::$authUsing = $callback;
    }
}
