<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Framework</title>
    <link rel="stylesheet" href="/dist/css/app.css">
</head>
<body>
    <header>
        <nav>
            <h1>{{ $app_name ?? 'Framework' }}</h1>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Welcome to Framework</h2>
            <p>Production-grade PHP MVC Framework</p>
        </section>

        <section class="features">
            <div class="feature">
                <h3>MVC Architecture</h3>
                <p>Clean separation of concerns with Models, Views, and Controllers</p>
            </div>

            <div class="feature">
                <h3>Multi-Database Support</h3>
                <p>MySQL, MariaDB, SQLite, and PostgreSQL with unified query builder</p>
            </div>

            <div class="feature">
                <h3>Blade Templates</h3>
                <p>Powerful template engine with layouts, components, and conditionals</p>
            </div>

            <div class="feature">
                <h3>ORM</h3>
                <p>Active record pattern with relationships and soft deletes</p>
            </div>

            <div class="feature">
                <h3>Security</h3>
                <p>CSRF protection, password hashing, input validation, and XSS escaping</p>
            </div>

            <div class="feature">
                <h3>CLI Tools</h3>
                <p>Artisan CLI for migrations, seeders, and code generation</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Framework. All rights reserved.</p>
    </footer>

    <script src="/dist/js/app.js"></script>
</body>
</html>
