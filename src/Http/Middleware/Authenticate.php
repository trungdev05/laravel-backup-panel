<?php

namespace Trungdev05\LaravelBackupPanel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Trungdev05\LaravelBackupPanel\LaravelBackupPanel;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (LaravelBackupPanel::check($request)) {
            return $next($request);
        }

        abort(403);
    }
}
