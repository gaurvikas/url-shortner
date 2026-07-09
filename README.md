# Sembark URL Shortener Assignment

A Laravel URL shortener with company-based access control, invitations, role-based dashboards, and public short URL redirects.

## Features

- Authentication with Laravel Breeze.
- Roles: SuperAdmin, Admin, Member.
- SuperAdmin can invite a company Admin and view all company URLs.
- Admin can invite Admin/Member users inside their own company.
- Admin can view all short URLs for their company.
- Member can create and view only their own short URLs.
- SuperAdmin cannot create short URLs.
- Public short URLs redirect to the original URL and increment visit counts.
- Feature tests.

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Laravel v12

## Local Setup

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Copy the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

4. Generate the app key:

```bash
php artisan key:generate
```

5. Create a new MySQL database and update your `.env` file with the correct database credentials.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=root
DB_PASSWORD=
```

6. Run the migrations and seed the database:

```bash
php artisan migrate --seed
```

7. Start the app:

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000`.

## SuperAdmin Login

- Email: `superadmin@sembark.test`
- Password: `password`


## Testing

Run the test suite:

```bash
php artisan test
```

Current verification: `36 passed (114 assertions)`.

## Usage Flow

1. Login as SuperAdmin.
2. Invite a new company Admin from the dashboard.
3. Copy the generated invitation link and open it in a browser.
4. Accept the invitation by setting a password.
5. Login as the company Admin and invite Members or other Admins.
6. Admins and Members can generate short URLs from the dashboard.