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
- Laravel 10.x

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
- GET /api/promo-codes - List all promo codes
- POST /api/promo-codes - Create a new promo code
- GET /api/promo-codes/{id} - Get promo code details
- PUT /api/promo-codes/{id} - Update a promo code
- DELETE /api/promo-codes/{id} - Delete a promo code
- POST /api/promo-codes/validate - Validate a promo code

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
