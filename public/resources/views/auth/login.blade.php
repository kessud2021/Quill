<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/dist/css/app.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 4rem auto;
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 1rem;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 0.25rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #5568d3;
        }

        .error {
            color: #d32f2f;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #d32f2f;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>

        @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="/login">
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="/register" style="color: #667eea;">Register here</a>
        </p>
    </div>
</body>
</html>
