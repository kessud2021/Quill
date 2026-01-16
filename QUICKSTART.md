# Quill Framework - Quick Start Guide

Welcome to Quill Framework! This guide will get you up and running in minutes.

## 📋 Requirements

- PHP 8.2+
- Composer
- SQLite, MySQL, MariaDB, or PostgreSQL (optional)

## 🚀 Installation

### 1. Clone/Setup
```bash
cd d:/Quill
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` with your settings:
```env
APP_NAME="My App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/app.db
```

### 4. Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## 📁 Project Structure

```
app/
  Controllers/       # Your controllers
  Models/           # Your models
  Requests/         # Form requests
  Providers/        # Service providers

config/
  app.php           # Application config
  database.php      # Database config
  auth.php          # Auth config

database/
  migrations/       # Schema migrations
  seeders/          # Database seeders

resources/
  views/            # Blade templates

routes/
  web.php           # Route definitions

public/
  index.php         # Application entry point

storage/
  logs/             # Application logs
```

## 🔧 Common Tasks

### Create a Controller
```bash
php artisan make:controller PostController
```

This creates `app/Controllers/PostController.php`

### Create a Model
```bash
php artisan make:model Post
```

This creates `app/Models/Post.php`

### Create a Migration
```bash
php artisan make:migration create_posts_table
```

This creates `database/migrations/TIMESTAMP_create_posts_table.php`

### Run Migrations
```bash
php artisan migrate
```

### Seed Database
```bash
php artisan seed
```

### Create a Request Class
```bash
php artisan make:request CreatePostRequest
```

### Create a Service Provider
```bash
php artisan make:provider AppServiceProvider
```

### Interactive Shell
```bash
php artisan tinker
```

## 📝 Define Routes

Edit `routes/web.php`:

```php
$router = app(\Framework\Routing\Router::class);

// Basic route
$router->get('/', 'HomeController@index')->name('home');

// Route with parameter
$router->get('/posts/{id}', 'PostController@show')->name('posts.show');

// Post route
$router->post('/posts', 'PostController@store')->name('posts.store');

// Resource route (creates 7 routes)
$router->resource('posts', 'PostController');

// Route group
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($r) {
    $r->get('/', 'AdminController@index')->name('admin.dashboard');
});
```

## 🎯 Create Your First Controller

Create `app/Controllers/PostController.php`:

```php
<?php

namespace App\Controllers;

use Framework\Foundation\Controller;
use Framework\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return $this->view('posts.index', compact('posts'));
    }

    public function create()
    {
        return $this->view('posts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create($data);
        return $this->redirectToRoute('posts.index');
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return $this->view('posts.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return $this->view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->fill($data)->save();
        return $this->redirectToRoute('posts.show', ['id' => $id]);
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return $this->redirectToRoute('posts.index');
    }
}
```

## 💾 Create Your First Model

Create `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use Framework\Database\Model;

class Post extends Model
{
    protected ?string $table = 'posts';
    protected array $fillable = ['title', 'content'];
}
```

## 🗄️ Create a Migration

`database/migrations/2024_01_16_000000_create_posts_table.php`:

```php
<?php

class CreatePostsTable
{
    public function up(): void
    {
        // Use raw SQL or query builder
        $sql = "CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            deleted_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        db()->getPdo()->exec($sql);
    }

    public function down(): void
    {
        db()->getPdo()->exec("DROP TABLE IF EXISTS posts");
    }
}
```

Run migrations:
```bash
php artisan migrate
```

## 📄 Create Views

`resources/views/posts/index.blade.php`:

```blade
@extends('layout')

@section('content')
    <h1>Posts</h1>
    <a href="{{ route('posts.create') }}">Create Post</a>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>
                        <a href="{{ route('posts.show', ['id' => $post->id]) }}">View</a>
                        <a href="{{ route('posts.edit', ['id' => $post->id]) }}">Edit</a>
                        <form method="POST" action="{{ route('posts.destroy', ['id' => $post->id]) }}" style="display:inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
```

`resources/views/posts/create.blade.php`:

```blade
@extends('layout')

@section('content')
    <h1>Create Post</h1>

    <form method="POST" action="{{ route('posts.store') }}">
        @csrf

        <div>
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div>
            <label for="content">Content</label>
            <textarea id="content" name="content" required>{{ old('content') }}</textarea>
        </div>

        <button type="submit">Create</button>
    </form>
@endsection
```

## 🧪 Using the Database

### Query Builder

```php
// Get all
$posts = db()->table('posts')->get();

// Get one
$post = db()->table('posts')->where('id', 1)->first();

// Count
$count = db()->table('posts')->count();

// Insert
db()->table('posts')->insert([
    'title' => 'Hello',
    'content' => 'World',
]);

// Update
db()->table('posts')
    ->where('id', 1)
    ->update(['title' => 'Updated']);

// Delete
db()->table('posts')->where('id', 1)->delete();

// Conditions
db()->table('posts')
    ->where('status', 'published')
    ->where('views', '>', 100)
    ->orWhere('featured', true)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

### Using Models

```php
use App\Models\Post;

// Get all
$posts = Post::all();

// Find by ID
$post = Post::find(1);
$post = Post::findOrFail(1);

// Find by column
$post = Post::findBy('slug', 'hello-world');

// Create
$post = Post::create([
    'title' => 'Hello',
    'content' => 'World',
]);

// Update
$post = Post::find(1);
$post->title = 'Updated';
$post->save();

// Delete (soft delete)
$post->delete();

// Restore
$post->restore();

// Force delete
$post->forceDelete();

// Query
$posts = Post::query()
    ->where('status', 'published')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

## 🔐 Authentication

```php
// Attempt login
if (auth()->attempt('user@example.com', 'password')) {
    // Login successful
    redirect('/dashboard');
}

// Check if authenticated
if (auth()->check()) {
    $user = auth()->user();
    echo $user->name;
}

// Get current user
$user = auth()->user();
$id = auth()->id();

// Logout
auth()->logout();
```

## ✅ Validation

```php
$data = $this->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|confirmed',
    'name' => 'required|string|max:255',
    'age' => 'numeric|min:18',
    'username' => 'required|unique:users,username',
]);
```

Available rules:
- `required` - Must not be empty
- `email` - Valid email
- `min:n` - Minimum length
- `max:n` - Maximum length
- `numeric` - Must be numeric
- `string` - Must be string
- `array` - Must be array
- `confirmed` - Must match field_confirmation
- `unique:table,column` - Value must be unique
- `exists:table,column` - Value must exist
- `regex:pattern` - Regex match

## 🔒 Security

### CSRF Protection

```blade
<form method="POST" action="/posts">
    @csrf
    <input type="text" name="title">
    <button type="submit">Submit</button>
</form>
```

Or programmatically:
```php
csrf_token();  // Get token
csrf_field();  // HTML field
```

### Password Hashing

```php
// Hash password
$hashed = hash_password('password123');

// Verify
if (verify_password('password123', $hashed)) {
    // Correct password
}
```

## 📊 Sessions

```php
// Store
session()->put('user_id', 123);
session()->put('user.name', 'John');

// Retrieve
$id = session()->get('user_id');
$name = session()->get('user.name', 'Guest');

// Check
if (session()->has('user_id')) {
    // ...
}

// Forget
session()->forget('user_id');

// Flash (one request)
session()->put('message', 'Saved!');

// All
$all = session()->all();

// Flush
session()->flush();
```

## 📝 Logging

```php
logger()->debug('Debug message');
logger()->info('Info message');
logger()->warning('Warning message');
logger()->error('Error message');

logger()->info('User action', ['user_id' => 123, 'action' => 'login']);
```

Logs go to: `storage/logs/app.log`

## 🎨 Blade Templates

### Variables
```blade
{{ $variable }}           {# Escaped #}
{!! $html !!}            {# Unescaped #}
```

### Conditionals
```blade
@if ($condition)
    <p>True</p>
@elseif ($other)
    <p>Other</p>
@else
    <p>False</p>
@endif

@unless ($condition)
    <p>Not true</p>
@endunless
```

### Loops
```blade
@foreach ($items as $item)
    <p>{{ $item->name }}</p>
@endforeach

@for ($i = 0; $i < 10; $i++)
    <p>{{ $i }}</p>
@endfor

@while ($condition)
    <p>Loop</p>
@endwhile
```

### Auth
```blade
@auth
    <p>Logged in as {{ auth()->user()->name }}</p>
@endauth

@guest
    <p><a href="/login">Login</a></p>
@endguest
```

### Include
```blade
@include('partials.header')
@include('partials.footer', ['name' => 'value'])
```

## 🚀 Useful Helpers

```php
app()                    // Get container
config('app.debug')      // Get config
env('APP_DEBUG')         // Get env var
request()->input('name') // Get input
response('text')         // Create response
json_response(['ok'])    // JSON
redirect('/url')         // Redirect
route('home')            // Route URL
url('/path')             // URL
view('name', $data)      // Create view
auth()->user()           // Current user
session()->get('key')    // Session
logger()->info('msg')    // Log
csrf_token()             // CSRF token
hash_password('pass')    // Hash password
```

## 📚 Documentation

For detailed documentation, see:
- `FRAMEWORK.md` - Complete API documentation
- `BUILD_SUMMARY.md` - Implementation summary
- `COMPLETION_REPORT.md` - Requirements verification

## 🆘 Common Issues

### Routes Not Working
- Make sure routes are in `routes/web.php`
- Check controller namespace
- Verify PHP is running with `php -S localhost:8000`

### Database Connection Fails
- Check `.env` configuration
- Verify database exists
- Check database permissions
- For SQLite: ensure `database/` directory exists

### Templates Not Found
- Check view path in `resources/views/`
- Use dot notation: `view('posts.index')`
- Files must match: `posts.index` → `posts/index.blade.php`

## 💡 Tips

1. **Development Mode** - Set `APP_DEBUG=true` in `.env` for error details
2. **Database** - Use SQLite for development (no setup needed)
3. **Routes** - Use named routes: `route('posts.show', ['id' => 1])`
4. **Validation** - Always validate user input
5. **Models** - Keep business logic in models
6. **Views** - Keep presentation logic in views
7. **Controllers** - Keep controllers lean and focused

## 🎯 Next Steps

1. Read `FRAMEWORK.md` for complete documentation
2. Create your first controller
3. Define your routes
4. Create your models
5. Build your views
6. Test your application
7. Deploy!

## 📞 Support

For issues or questions:
1. Check the documentation
2. Review example code
3. Check error logs in `storage/logs/`
4. Enable debug mode in `.env`

---

**Happy coding with Quill Framework!** 🚀
