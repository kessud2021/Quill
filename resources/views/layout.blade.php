<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quill App' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .header h1 {
            font-size: 2em;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .footer {
            background: white;
            border-top: 1px solid #ddd;
            padding: 2rem 0;
            margin-top: 4rem;
            text-align: center;
            color: #666;
        }
        main {
            background: white;
            padding: 2rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>{{ $appName ?? 'Quill' }}</h1>
        </div>
    </div>

    <div class="container">
        <main>
            @yield('content')
        </main>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ $appName ?? 'Quill App' }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
