# E-Commerce Marketplace

A multi-role marketplace for selling digital products, built with Laravel 12, Blade, Tailwind CSS, Alpine.js, and Vite.

The application supports buyers, creators, affiliates, content reviewers, and super administrators. It includes product discovery, carts and wishlists, digital licensing and downloads, coupon handling, affiliate attribution, content moderation, and Cambodian payment integrations.

## Features

- Public marketplace with product search, categories, creator pages, and reviews
- Buyer cart, wishlist, checkout, order history, and licensed download library
- Creator product management, review responses, and content-report counter notices
- Affiliate referral links, commission tracking, and payout requests
- Content-review workflow for reports, takedowns, restorations, and hidden reviews
- Super-admin management for products, coupons, affiliates, orders, users, roles, and permissions
- Role- and permission-based access control
- Google OAuth sign-in
- Bakong KHQR payments and optional ABA PayWay card checkout
- Automated feature tests for core marketplace and authorization workflows

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL, PostgreSQL, or SQLite
- Blade and Alpine.js
- Tailwind CSS
- Vite
- PHPUnit

## Local Setup

### Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js and npm
- A supported database

### Installation

```bash
git clone https://github.com/xyrvt4mwzk-ops/ecommerce-project.git
cd ecommerce-project/e-commerce
composer setup
```

The `composer setup` command installs PHP and JavaScript dependencies, creates `.env`, generates the application key, runs migrations, and builds the frontend assets.

If you prefer to configure each step manually:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate --seed
npm run build
```

Update the database settings in `.env` before running migrations when you are not using the default SQLite configuration.

### Run the Application

```bash
composer dev
```

This starts the Laravel server, queue listener, application log viewer, and Vite development server. Open `http://127.0.0.1:8000`.

## Demo Data

Database seeding in the local environment creates marketplace demo content, roles and permissions, and a local super-admin account.

Default local administrator credentials:

```text
Email: admin@example.com
Password: password
```

Override them before seeding with `SUPER_ADMIN_NAME`, `SUPER_ADMIN_EMAIL`, and `SUPER_ADMIN_PASSWORD` in `.env`. Never use the default password in a deployed environment.

## Optional Integrations

### Google OAuth

Set these values in `.env`:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### Bakong KHQR

Configure a Bakong API token and merchant details:

```dotenv
BAKONG_API_TOKEN=
BAKONG_ACCOUNT_ID=
BAKONG_MERCHANT_NAME=
BAKONG_MERCHANT_CITY="Phnom Penh"
BAKONG_CURRENCY=USD
```

`BAKONG_ACCOUNT_ID` must be a Bakong receiver ID, such as `merchant@abaa`, rather than an ABA account number.

### ABA PayWay

Card payments are disabled by default. To enable the integration, provide sandbox or production credentials:

```dotenv
ABA_PAYWAY_ENABLED=true
ABA_PAYWAY_ENV=sandbox
ABA_PAYWAY_MERCHANT_ID=
ABA_PAYWAY_API_KEY=
ABA_PAYWAY_CURRENCY=USD
```

See `.env.example` for the complete configuration and endpoint variables.

## Testing

```bash
composer test
```

To run a specific test file:

```bash
php artisan test tests/Feature/CheckoutPageTest.php
```

## Production Notes

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Configure a production database, mail provider, queue worker, and scheduler.
- Build optimized assets with `npm run build`.
- Run `php artisan migrate --force` during deployment.
- Keep `.env`, payment credentials, OAuth secrets, and generated application keys out of version control.

## License

This project is built on Laravel, which is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
