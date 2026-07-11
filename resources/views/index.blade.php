@extends('laravel_backup_panel::layout')

@section('content')
    <main class="container mb-5">
        <div class="d-flex align-items-end pt-4">
            <h1 class="h5 mb-0">{{ __('laravel_backup_panel::panel.title') }}</h1>

            <form method="POST" action="{{ route('laravel-backup-panel.backups.store') }}" class="ms-auto">
                @csrf
                <div class="btn-group">
                    <button class="btn btn-primary btn-sm px-3" name="mode" value="full" type="submit">{{ __('laravel_backup_panel::panel.create_backup') }}</button>
                    <button class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">{{ __('laravel_backup_panel::panel.more_backup_options') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" name="mode" value="only-db" type="submit">{{ __('laravel_backup_panel::panel.create_database_backup') }}</button></li>
                        <li><button class="dropdown-item" name="mode" value="only-files" type="submit">{{ __('laravel_backup_panel::panel.create_files_backup') }}</button></li>
                    </ul>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div class="toast text-bg-success border-0" data-laravel-backup-panel-toast role="status" aria-live="polite" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ __('laravel_backup_panel::panel.close') }}"></button>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mt-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <section class="card shadow-sm mt-4 mb-4">
            <div class="card-header d-flex align-items-end">
                <a class="btn btn-primary btn-sm ms-auto px-3" href="{{ route('laravel-backup-panel.index') }}">{{ __('laravel_backup_panel::panel.refresh') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>{{ __('laravel_backup_panel::panel.disk') }}</th><th>{{ __('laravel_backup_panel::panel.healthy') }}</th><th>{{ __('laravel_backup_panel::panel.amount_of_backups') }}</th><th>{{ __('laravel_backup_panel::panel.newest_backup') }}</th><th>{{ __('laravel_backup_panel::panel.used_storage') }}</th></tr></thead>
                    <tbody>
                        @forelse($backupStatuses as $backupStatus)
                            @php($backupDestination = $backupStatus->backupDestination())
                            @php($newestBackup = $backupDestination->newestBackup())
                            <tr>
                                <td>{{ $backupDestination->diskName() }}</td>
                                <td>{{ $backupStatus->isHealthy() ? __('laravel_backup_panel::panel.yes') : __('laravel_backup_panel::panel.no') }}</td>
                                <td>{{ $backupDestination->backups()->count() }}</td>
                                <td>@if($newestBackup === null) {{ __('laravel_backup_panel::panel.no_backups_present') }} @else {{ $newestBackup->date()->diffForHumans() }} @endif</td>
                                <td>{{ \Spatie\Backup\Helpers\Format::humanReadableSize($backupDestination->usedStorage()) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="5">{{ __('laravel_backup_panel::panel.no_backup_disks_configured') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card shadow-sm">
            <div class="card-header d-flex align-items-end gap-2">
                @if($backupStatuses->isNotEmpty())
                    <div class="btn-group flex-wrap" role="group" aria-label="{{ __('laravel_backup_panel::panel.backup_disks') }}">
                        @foreach($backupStatuses as $backupStatus)
                            @php($disk = $backupStatus->backupDestination()->diskName())
                            <a class="btn btn-outline-secondary {{ $activeDisk === $disk ? 'active' : '' }}" href="{{ route('laravel-backup-panel.index', ['disk' => $disk]) }}">{{ $disk }}</a>
                        @endforeach
                    </div>
                @endif
                @if($activeDisk === null)
                    <button class="btn btn-primary btn-sm ms-auto px-3" type="button" disabled>{{ __('laravel_backup_panel::panel.refresh') }}</button>
                @else
                    <a class="btn btn-primary btn-sm ms-auto px-3" href="{{ route('laravel-backup-panel.index', ['disk' => $activeDisk]) }}">{{ __('laravel_backup_panel::panel.refresh') }}</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>{{ __('laravel_backup_panel::panel.path') }}</th><th>{{ __('laravel_backup_panel::panel.created_at') }}</th><th>{{ __('laravel_backup_panel::panel.size') }}</th><th></th></tr></thead>
                    <tbody>
                        @forelse($files as $backup)
                            <tr>
                                <td>{{ $backup->path() }}</td>
                                <td>{{ $backup->date()->format('Y-m-d H:i:s') }}</td>
                                <td>{{ \Spatie\Backup\Helpers\Format::humanReadableSize($backup->sizeInBytes()) }}</td>
                                <td class="text-end pe-3 text-nowrap">
                                    <a class="action-button me-2" href="{{ route('laravel-backup-panel.backups.download', ['disk' => $activeDisk, 'path' => $backup->path()]) }}" target="_blank">{{ __('laravel_backup_panel::panel.download') }}</a>
                                    <form method="POST" action="{{ route('laravel-backup-panel.backups.destroy', ['disk' => $activeDisk, 'path' => $backup->path()]) }}" class="d-inline" data-laravel-backup-panel-delete-form data-backup-name="{{ basename($backup->path()) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link action-button p-0" type="submit">{{ __('laravel_backup_panel::panel.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="4">{{ __('laravel_backup_panel::panel.no_backups_present') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal fade" id="laravel-backup-panel-delete-modal" tabindex="-1" aria-labelledby="laravel-backup-panel-delete-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-body"><h2 class="h5 mb-3" id="laravel-backup-panel-delete-title">{{ __('laravel_backup_panel::panel.delete_backup') }}</h2><span class="text-muted" data-laravel-backup-panel-delete-message data-message-template="{{ __('laravel_backup_panel::panel.delete_confirmation', ['backup' => ':backup']) }}"></span></div>
            <form method="POST" id="laravel-backup-panel-delete-confirmation">
                @csrf
                @method('DELETE')
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('laravel_backup_panel::panel.cancel') }}</button><button type="submit" class="btn btn-danger">{{ __('laravel_backup_panel::panel.delete') }}</button></div>
            </form>
        </div></div>
    </div>
@endsection
