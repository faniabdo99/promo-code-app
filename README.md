# Promo Code Application

A Laravel-based application for managing and validating promotional codes.

## Features

- User authentication and authorization
- Promo code management
- Promo code validation and usage tracking
- RESTful API endpoints for promo code operations

## Project Structure

```
app/
├── Models/
│   ├── PromoCode.php
│   ├── PromoCodeUsage.php
│   └── User.php
├── Http/
│   └── Controllers/
│       ├── AuthController.php
│       ├── PromoCodeController.php
│       └── Controller.php
└── Providers/

tests/
├── Feature/
│   ├── Auth/
│   └── PromoCode/
└── Unit/
```

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Laravel 12.x

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd promo-code-app
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in the `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

Or You can use Laravel Sail to run the docker version
```bash
./vendor/bin/sail up
```

Make sure to run the migrations using the following command
```bash
./vendor/bin/sail artisan migrate
```

## Testing

Run the test suite:
```bash
php artisan test
```

## API Endpoints

### Authentication
- POST /api/auth/register - Register a new user
- POST /api/auth/login - Login user

### Promo Codes
- POST /api/promo-codes - Create a new promo code
- POST /api/promo-codes/redeem - Redeem a promo code