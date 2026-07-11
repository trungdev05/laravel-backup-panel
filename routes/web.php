<?php

use Illuminate\Support\Facades\Route;
use PavelMironchik\LaravelBackupPanel\Actions\CreateBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\DeleteBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\DownloadBackupAction;
use PavelMironchik\LaravelBackupPanel\Actions\ShowBackupPanelAction;

Route::get('/', ShowBackupPanelAction::class)->name('laravel-backup-panel.index');
Route::post('/backups', CreateBackupAction::class)->name('laravel-backup-panel.backups.store');
Route::get('/backups/download', DownloadBackupAction::class)->name('laravel-backup-panel.backups.download');
Route::delete('/backups', DeleteBackupAction::class)->name('laravel-backup-panel.backups.destroy');
