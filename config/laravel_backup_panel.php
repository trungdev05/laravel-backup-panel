<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Backup Panel Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Laravel Backup Panel will be accessible from.
    | Feel free to change this path to anything you like.
    |
    |
    */

    'path' => 'backup',

    /*
    |--------------------------------------------------------------------------
    | Queue To Run Backup Jobs
    |--------------------------------------------------------------------------
    |
    | You can specify a queue name to use for the jobs to run through.
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
    | These middleware are appended after the package's mandatory `web` and
    | authorization middleware. Use this for application-specific controls,
    | such as authentication, abilities, or an IP allowlist.
    |
    */

    'middleware' => [],

];
