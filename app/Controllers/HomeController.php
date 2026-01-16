<?php

namespace App\Controllers;

use Framework\Foundation\Controller;
use Framework\Http\Response;

/**
 * Home Controller
 * 
 * Handles home page requests
 */
class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return Response
     */
    public function index(): Response
    {
        $appName = config('app.name');
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {$appName}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        .features {
            margin: 30px 0;
            text-align: left;
        }
        .features h2 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 15px;
            text-align: center;
        }
        .feature-list {
            list-style: none;
        }
        .feature-list li {
            color: #555;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 10px;
        }
        .cta {
            margin-top: 30px;
        }
        .cta a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.3s;
            margin: 5px;
        }
        .cta a:hover {
            transform: translateY(-2px);
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Welcome to {$appName}</h1>
        
        <p>A production-grade PHP framework inspired by Laravel, built from scratch.</p>
        
        <div class="features">
            <h2>Features</h2>
            <ul class="feature-list">
                <li>Service Container & Dependency Injection</li>
                <li>Full-featured Router with Named Routes</li>
                <li>Eloquent-like Active Record ORM</li>
                <li>Blade Template Engine</li>
                <li>Database Query Builder</li>
                <li>Input Validation</li>
                <li>Session Management</li>
                <li>Authentication System</li>
                <li>CSRF Protection</li>
                <li>Artisan CLI Commands</li>
                <li>Support for SQLite, MySQL, MariaDB, PostgreSQL</li>
                <li>Structured Logging</li>
            </ul>
        </div>
        
        <div class="cta">
            <a href="https://github.com/kessud2021/Quill" target="_blank">📖 View on GitHub</a>
        </div>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #999;">
            Start building with <code>php artisan make:controller YourController</code>
        </p>
    </div>
</body>
</html>
HTML;

        return response($html);
    }
}
