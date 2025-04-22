<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BaseSSO Documentation</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        h1, h2, h3, h4 {
            color: #1a1a1a;
            margin-top: 2rem;
        }
        code {
            background-color: #f5f5f5;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background-color: #f5f5f5;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
        }
        a {
            color: #F53003;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="documentation">
        {!! $content !!}
    </div>
</body>
</html>
