<?php

namespace PavelMironchik\LaravelBackupPanel\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

final class PanelConfiguration
{
    public function path(): string
    {
        return $this->nonEmptyString(
            Config::string('laravel_backup_panel.path'),
            'laravel_backup_panel.path',
        );
    }

    public function queue(): string
    {
        $queue = $this->nonEmptyString(
            Config::string('laravel_backup_panel.queue'),
            'laravel_backup_panel.queue',
        );

        $this->validateQueueConnection();

        return $queue;
    }

    /** @return list<string> */
    public function middleware(): array
    {
        $middleware = Config::array('laravel_backup_panel.middleware');

        if (! array_is_list($middleware)) {
            throw new InvalidArgumentException('Configuration [laravel_backup_panel.middleware] must be a list of middleware strings.');
        }

        foreach ($middleware as $name) {
            if (! is_string($name) || $name === '') {
                throw new InvalidArgumentException('Configuration [laravel_backup_panel.middleware] must contain only non-empty middleware strings.');
            }
        }

        return $middleware;
    }

    private function validateQueueConnection(): void
    {
        $connection = $this->nonEmptyString(Config::string('queue.default'), 'queue.default');

        if ($connection === 'null') {
            throw new InvalidArgumentException('Laravel Backup Panel requires queue.default to use a driver other than [sync] or [null].');
        }

        $driver = $this->nonEmptyString(
            Arr::string(Config::array("queue.connections.{$connection}"), 'driver'),
            "queue.connections.{$connection}.driver",
        );

        if (in_array($driver, ['sync', 'null'], true)) {
            throw new InvalidArgumentException('Laravel Backup Panel requires queue.default to use a driver other than [sync] or [null].');
        }
    }

    private function nonEmptyString(string $value, string $source): string
    {
        if ($value === '') {
            throw new InvalidArgumentException("Configuration [{$source}] must be a non-empty string.");
        }

        return $value;
    }
}
