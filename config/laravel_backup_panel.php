<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Backup Panel Path
    |--------------------------------------------------------------------------
    |
    | This non-empty URI path is where Laravel Backup Panel is accessible.
    |
    |
    */

    'path' => 'backup',

    /*
    |--------------------------------------------------------------------------
    | Queue To Run Backup Jobs
    |--------------------------------------------------------------------------
    |
    | This non-empty queue name is used for backup jobs. The application's
    | default queue connection must use a driver other than `sync` or `null`.
    |
    | Useful when you don't want to run backup jobs on the same queue as user
    | actions and things that is more time critical.
    |
    */

    'queue' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Additional Route Middleware
    |--------------------------------------------------------------------------
    |
    | This must be a list of non-empty middleware strings. They run after the
    | package's mandatory `web` middleware and before package authorization.
    | Use this for application-specific controls, such as authentication,
    | abilities, or an IP allowlist.
    |
    */

    'middleware' => [],

];
