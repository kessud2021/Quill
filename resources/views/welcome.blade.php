<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - {{ $appName ?? 'Quill' }}</title>
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
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-size: 1.5em;
            color: #667eea;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        h1, h2 {
            color: #333;
            margin: 2rem 0 1rem 0;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .code-block code {
            color: inherit;
            background: none;
            padding: 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1>{{ $appName ?? 'Quill' }}</h1>
            @if (auth()->check())
                <div>
                    Welcome, {{ auth()->user()->name }}!
                </div>
            @endif
        </div>
    </div>

    <div class="container">
        <h1>Welcome to Quill Framework</h1>

        <p>This is a demonstration view using Blade template syntax.</p>

        <h2>What's Next?</h2>

        <ol>
            <li>Create your first controller: <code>php artisan make:controller PostController</code></li>
            <li>Generate a model: <code>php artisan make:model Post</code></li>
            <li>Create a migration: <code>php artisan make:migration create_posts_table</code></li>
            <li>Add routes in <code>routes/web.php</code></li>
            <li>Create views in <code>resources/views</code></li>
        </ol>

        <h2>Framework Structure</h2>

        <div class="code-block">
            <code>
app/
├── Controllers/
├── Models/
└── Providers/

config/
├── app.php
├── database.php
└── auth.php

database/
├── migrations/
└── seeders/

resources/
└── views/

routes/
└── web.php

src/
├── Auth/
├── Config/
├── Console/
├── Container/
├── Database/
├── Env/
├── Exception/
├── Foundation/
├── Http/
├── Logging/
├── Middleware/
├── Routing/
├── Security/
├── Session/
├── Support/
├── Validation/
└── View/
            </code>
        </div>

        <h2>Learn More</h2>

        <p>
            Check out the <a href="https://github.com/kessud2021/Quill" target="_blank">GitHub repository</a>
            for documentation and examples.
        </p>
    </div>
</body>
</html>
