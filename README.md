# Sembark URL Shortener Assignment

A Laravel 12 URL shortener with company-based access control, invitations, role-based dashboards, and public short URL redirects.

## Features

- Authentication with Laravel Breeze.
- Roles: SuperAdmin, Admin, Member.
- SuperAdmin can invite a company Admin and view all company URLs.
- Admin can invite Admin/Member users inside their own company.
- Admin can view all short URLs for their company.
- Member can create and view only their own short URLs.
- SuperAdmin cannot create short URLs.
- Public short URLs redirect to the original URL and increment visit counts.
- Feature tests cover the assignment requirements.

## Local Setup

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Copy the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

5. Generate the app key:

```bash
php artisan key:generate
```

6. Create the SQLite database file if it does not exist:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

7. Run migrations and seed the SuperAdmin account:

```bash
php artisan migrate --seed
```

8. Build frontend assets:

```bash
npm run build
```

9. Start the app:

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000`.


## SuperAdmin Login

- Email: `superadmin@sembark.test`
- Password: `password`


The SuperAdmin account is created by `DatabaseSeeder` using raw SQL insert statements.

## Testing

Run the test suite:

```bash
php artisan test
```

Current verification: `36 passed (114 assertions)`.

## Usage Flow

1. Login as SuperAdmin.
2. Invite a new company Admin from the dashboard.
3. Copy the generated invitation link and open it in a guest browser/session.
4. Accept the invitation by setting a password.
5. Login as the company Admin and invite Members or other Admins.
6. Admins and Members can generate short URLs from the dashboard.