<?php

use Illuminate\Support\Facades\Route;
use Trungdev05\LaravelBackupPanel\Actions\CreateBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\DeleteBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\DownloadBackupAction;
use Trungdev05\LaravelBackupPanel\Actions\ShowBackupPanelAction;

Route::get('/', ShowBackupPanelAction::class)->name('laravel-backup-panel.index');
Route::post('/backups', CreateBackupAction::class)->name('laravel-backup-panel.backups.store');
Route::get('/backups/download', DownloadBackupAction::class)->name('laravel-backup-panel.backups.download');
Route::delete('/backups', DeleteBackupAction::class)->name('laravel-backup-panel.backups.destroy');
