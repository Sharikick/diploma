<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/style.css">
        <title>{{ $title }}</title>
    </head>
    <body class="app">
        <x-header />
        <main class="main">
            {{$slot}}
        </main>
    </body>
</html>
