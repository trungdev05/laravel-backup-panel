# Laravel Backup Panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/trungdev05/laravel-backup-panel.svg?style=flat-square)](https://packagist.org/packages/trungdev05/laravel-backup-panel)
[![CI](https://github.com/trungdev05/laravel-backup-panel/actions/workflows/ci.yml/badge.svg)](https://github.com/trungdev05/laravel-backup-panel/actions/workflows/ci.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/trungdev05/laravel-backup-panel.svg?style=flat-square)](https://packagist.org/packages/trungdev05/laravel-backup-panel)

Laravel Backup Panel provides a dashboard for [spatie/laravel-backup](https://github.com/spatie/laravel-backup) package.
It lets you:
- create a backup (full | only database | only files)
- check the health of your backups
- list all backups
- download a backup
- delete a backup
- monitor used disk storage

![Screenshot](https://i.imgur.com/jrqTPuJ.png)

It resembles the look and functionality of another Spatie package: [spatie/nova-backup-tool](https://github.com/spatie/nova-backup-tool).
This was done on purpose, so users can easily migrate from one to another.
Only it doesn't use polling.
_A "real-time" updates of a backups list isn't such a necessarily thing and an intensive polling can cause unexpected charges if you use services that require to pay per API requests, such as Google Cloud Storage.
Also, some users reported about hitting a rate limit of Dropbox API._

## Requirements

Laravel Backup Panel 3 requires PHP 8.3 or newer, Laravel 12.40 or 13, and spatie/laravel-backup 10.

## Installation

First install [spatie/laravel-backup](https://github.com/spatie/laravel-backup) into your Laravel app.
When successful, running `php artisan backup:run` on the terminal should create a backup and `php artisan backup:list` should return a list with an overview of all backup disks.

You may use composer to install Laravel Backup Panel into your project:

```bash
$ composer require trungdev05/laravel-backup-panel
```

After installing, publish its resources using provided Artisan command:

```bash
$ php artisan laravel-backup-panel:install
```

This will do the following:
- place Bootstrap CSS and JavaScript files into `public/vendor/laravel_backup_panel` directory
- place Blade templates into `resources/views/vendor/laravel_backup_panel` directory
- add config file `config/laravel_backup_panel.php`
- register service provider in `bootstrap/providers.php`

### Updating

When updating the package, do not forget to re-publish resources:

```bash
$ php artisan vendor:publish --tag=laravel-backup-panel-assets --force
$ php artisan vendor:publish --tag=laravel-backup-panel-views --force
```

## Configuration

The panel currently uses English-only UI text.

You are free to tune CSS styles in the `public/vendor/laravel_backup_panel` directory and change the layout in the `resources/views/vendor/laravel_backup_panel` directory as you want.

Laravel Backup Panel exposes a dashboard at `/backup`. Change it in `config/laravel_backup_panel.php` file:

```php
'path' => 'backup',
```

The panel requires non-empty `path` and `queue` strings. Its default queue connection must use a driver other than `sync` or `null`, and a worker must consume the configured queue. Invalid panel or queue configuration fails before a request is accepted, so the success message always represents an asynchronous request. It permits one backup request at a time through Laravel's unique-job lock; multi-worker or multi-server deployments must use a shared cache store that supports atomic locks. Sometimes you don't want to run backup jobs on the same queue as user actions and things that is more time critical. Specify your desired queue name in `config/laravel_backup_panel.php` file:

```php
'queue' => 'dedicated_low_priority_queue',
```

The dashboard authorization is fail-closed: it returns `403` until the service provider created by the install command is registered. Application middleware runs before package authorization, so it can establish a guard and enforce an authenticated user, an ability, or a network restriction:

```php
'middleware' => ['auth', 'can:access-backup-panel'],
```

By default, you will only be able to access the dashboard in the `local` environment. 
To change that, modify authorization gate in the `app/Providers/LaravelBackupPanelServiceProvider.php`:

```php
use App\Models\User;

/**
 * Register the Laravel Backup Panel gate.
 *
 * This gate determines who can access Laravel Backup Panel in non-local environments.
 *
 */
protected function gate(): void
{
    Gate::define('viewLaravelBackupPanel', static function (User $user): bool {
        return in_array($user->email, [
            'admin@your-site.com',
        ]);
    });
}
```

The `middleware` list must contain only non-empty strings. It runs after the package's mandatory `web` middleware and before package authorization; it cannot remove package authorization.

The panel manages exactly the backup declared by `backup.backup`: `backup.monitor_backups` must contain exactly one entry with the same `name` and the same ordered `disks` list. This makes health status, listing, download, and deletion refer to one backup contract. Additional monitor entries for other applications are allowed but are not shown in the panel.

## Usage

Open `http://your-site/backup`. You'll see a dashboard and controls to use.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Testing

```bash
$ composer test
```

### Security

Please read the [security policy](SECURITY.md). Report vulnerabilities privately; do not open a public issue.

## Support

Please read [SUPPORT](SUPPORT.md) for usage questions and help.

## Credits

- [trungdev05](https://github.com/trungdev05)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
