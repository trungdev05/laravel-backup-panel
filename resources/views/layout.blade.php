<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Backup Panel</title>

    <link href="{{ asset('vendor/laravel_backup_panel/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/laravel_backup_panel/css/app.css') }}" rel="stylesheet">
</head>
<body>
    @yield('content')

    <script src="{{ asset('vendor/laravel_backup_panel/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/laravel_backup_panel/js/app.js') }}"></script>
</body>
</html>
