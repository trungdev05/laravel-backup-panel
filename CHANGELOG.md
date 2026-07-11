# Changelog

All notable changes to `laravel-backup-panel` will be documented in this file

## 3.0.0 - Unreleased

- Establish `trungdev05/laravel-backup-panel` as the package identity and rename the PHP namespace to `Trungdev05\\LaravelBackupPanel`
- Add support for PHP 8.3+, Laravel 12.40 through 13, and spatie/laravel-backup 10
- Use one explicit Spatie Backup contract without compatibility adapters
- Replace Livewire with server-rendered Blade views and progressive JavaScript
- Upgrade bundled assets to local Bootstrap 5.3.8 and remove remote frontend dependencies
- Deny dashboard access safely until its application authorization provider is registered
- Run application-specific route middleware before package authorization without removing it
- Keep UI, validation, and flash-message strings English-only
- Require one monitor entry that exactly matches the managed backup name and disks
- Run queued backups through Spatie's command lifecycle and prevent concurrent backup requests
- Validate panel path, queue, middleware, and queue driver configuration at their boundaries

## 2.2.0 - 2022-05-05

- Add support for spatie/laravel-backup ^8.0
- Assign a name for a route, allowing adding custom middlewares to it
- PHP dependencies were updated to eliminate vulnerabilities found in the previous versions of libraries

## 2.1.1 - 2021-05-19

- Fix the Laravel 8 compatibility issue

## 2.1.0 - 2021-04-06

- Add support for spatie/laravel-backup ^7.0

## 2.0.0 - 2021-01-24

- Use Laravel Livewire instead of VueJS
- Allow to customize the layout and the styles 

## 1.5.1 - 2021-01-22

- JavaScript dependencies were updated to eliminate vulnerabilities found in the previous versions of libraries
- Laravel Mix was updated to version 6

## 1.5.0 - 2020-04-02

- Show warning when assets are outdated (it is recommended to run `php artisan vendor:publish --tag=laravel-backup-panel-assets --force` after updating)

## 1.4.0 - 2020-03-08

- Add support for Laravel 7

## 1.3.1 - 2020-02-16

- Allow to use a custom queue

## 1.3.0 - 2020-01-11

- Add Artisan command for installing the package

## 1.2.0 - 2020-01-09

- Resemble look and functionality of Spatie's Laravel Nova Backup tool

## 1.1.0 - 2020-01-07

- Restrict access in non-local environments by default
- Change naming of classes, namespaces, files, etc. to fit project's name "Laravel Backup Panel"

## 1.0.0 - 2020-01-05

- Initial release
