<?php

use Illuminate\Support\Facades\Route;
use PavelMironchik\LaravelBackupPanel\Http\Controllers\BackupController;

Route::get('/', [BackupController::class, 'index'])->name('laravel-backup-panel.index');
Route::post('/backups', [BackupController::class, 'store'])->name('laravel-backup-panel.backups.store');
Route::get('/backups/download', [BackupController::class, 'download'])->name('laravel-backup-panel.backups.download');
Route::delete('/backups', [BackupController::class, 'destroy'])->name('laravel-backup-panel.backups.destroy');
