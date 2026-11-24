<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @yield('styles')
</head>
<body>
    @yield('header')
    <main>
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>