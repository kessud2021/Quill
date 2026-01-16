# Quill - Production-Grade PHP MVC Framework

A modern PHP MVC framework with integrated frontend asset pipeline, REST API, and authentication system.

## Features

- **MVC Architecture** - Clean separation of concerns
- **REST API** - Built-in user management endpoints
- **Authentication** - Login/register system with session management
- **Database Agnostic** - Support for SQLite, MySQL, MariaDB, PostgreSQL
- **Frontend Pipeline** - Webpack-based asset compilation
- **Command Line Interface** - Artisan commands for migrations, seeding, and development

## Requirements

- PHP >= 8.2
- Node.js (for frontend asset building)
- Composer
- npm or yarn

### PHP Extensions
- pdo
- json
- filter
- hash

## Installation

### 1. Clone & Setup Environment

```bash
git clone https://github.com/kessud2021/Quill.git
cd Quill
cp .env.example .env
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Build Frontend Assets

```bash
npm run build    # Production build
npm run dev      # Development with watch mode
```

### 4. Setup Database

```bash
php artisan migrate
php artisan seed
```

### 5. Start Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Development

### Run in Development Mode

```bash
# Terminal 1: PHP development server
php artisan serve

# Terminal 2: Watch frontend assets
npm run dev
```

### Run Tests

```bash
composer test
```

## API Endpoints

### Users

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List all users |
| POST | `/api/users` | Create user |
| GET | `/api/users/{id}` | Get user by ID |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Delete user |

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/login` | Login form |
| POST | `/login` | Submit login |
| GET | `/register` | Register form |
| POST | `/register` | Submit registration |
| GET | `/logout` | Logout user |

## Configuration

### Database

Edit `.env` to configure your database:

```env
DB_CONNECTION=sqlite  # sqlite, mysql, mariadb, pgsql
DB_DATABASE=database.sqlite

# For MySQL/MariaDB/PostgreSQL:
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
```

Supported drivers:
- **SQLite** (default) - `sqlite`
- **MySQL** - `mysql`
- **MariaDB** - `mariadb`
- **PostgreSQL** - `pgsql`

## Project Structure

```
Quill/
├── app/                  # Application code (controllers, models, services)
├── bootstrap/            # Bootstrap files
├── config/              # Configuration files
├── database/            # Migrations and seeds
├── routes/              # Route definitions
├── src/                 # Framework core code
├── storage/             # Logs and caches
├── tests/               # Test files
├── public/              # Web root (compiled assets)
├── webpack.config.js    # Webpack configuration
├── composer.json        # PHP dependencies
├── package.json         # Node.js dependencies
└── artisan              # CLI entry point
```

## Available Commands

```bash
php artisan migrate          # Run pending migrations
php artisan seed             # Seed database with sample data
php artisan serve            # Start development server
npm run build                # Build production assets
npm run dev                  # Build & watch assets in dev mode
composer test                # Run tests
```

## Building for Production

```bash
npm run build       # Minified frontend assets
php artisan migrate # Apply migrations
```

## License

MIT License - See LICENSE file for details

## Support

For issues and questions, please open an issue on GitHub: https://github.com/kessud2021/Quill/issues
