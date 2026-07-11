<?php

namespace PavelMironchik\LaravelBackupPanel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PavelMironchik\LaravelBackupPanel\LaravelBackupPanel;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (LaravelBackupPanel::check($request)) {
            return $next($request);
        }

        abort(403);
    }
}
