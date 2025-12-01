# BRMS - Barangay Residency Management System

A comprehensive web-based management system for barangay operations, built with Laravel 11 and modern web technologies.

## About BRMS

BRMS is a complete solution for managing barangay residents, households, certificate requests, and administrative operations. The system provides role-based access control for Admins, Clerks, and Residents with a clean, modern interface.

## Key Features

### For Residents
- **Self-Registration**: Register with email or phone verification
- **Certificate Requests**: Request barangay certificates online
- **Profile Management**: Update personal information and contact details
- **Request Tracking**: Monitor certificate request status in real-time

### For Staff (Admins & Clerks)
- **Resident Management**: Manage resident records and households
- **Certificate Processing**: Review, approve, or reject certificate requests
- **Record Archiving**: Generate and manage Excel/CSV exports of resident and household data
- **Activity Logs**: Track all system activities for audit purposes

### Admin-Only Features
- **User Management**: Manage resident accounts and staff accounts
- **Verification System**: Approve/reject registration requests with bulk actions
- **Backup & Restore**: Database backup and restore functionality
- **Certificate Fee Configuration**: Configure fees for different certificate types

## Technology Stack

- **Framework**: Laravel 12
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Language:** PHP 8.2+
- **Database**: MySQL
- **Queue System**: Database-driven queues for email notifications
- **Cache**: Database cache driver
- **File Storage**: Local disk storage for documents

## Installation

### Requirements
- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/MJ-2502/ADBS-BRMS.git
   cd adbs-project
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file**
   ```env
   APP_NAME=BRMS
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=brms
   DB_USERNAME=root
   DB_PASSWORD=your_password
   
   QUEUE_CONNECTION=database
   CACHE_STORE=database
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - URL: `http://localhost:8000`
   - Default Admin: `admin@brms.local` / `password` (if auto-bootstrap is enabled)

## User Roles & Permissions

### Admin
- Full system access
- User management (residents & staff)
- Registration verification & approval
- Backup & restore operations
- Certificate fee configuration

### Clerk
- Resident & household management
- Certificate request processing
- Record archiving
- Activity log viewing

### Resident
- Profile management
- Certificate requests
- Request status tracking

## Development

### Running Queue Workers
```bash
php artisan queue:work
```

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

## Project Structure

```
app/
├── Enums/              # Status enums (CertificateStatus, UserRole, etc.)
├── Http/
│   ├── Controllers/    # Application controllers
│   ├── Middleware/     # Custom middleware
│   └── Requests/       # Form request validation
├── Models/             # Eloquent models
├── Notifications/      # Email/SMS notifications
└── Services/           # Business logic services

resources/
├── views/              # Blade templates
│   ├── auth/          # Authentication views
│   ├── certificates/  # Certificate management
│   ├── accounts/      # User management
│   └── layouts/       # Layout templates
└── js/                 # Frontend JavaScript

database/
├── migrations/         # Database migrations
└── seeders/           # Database seeders
```

## Security Features

- Email/Phone verification for registration
- Role-based access control (RBAC)
- CSRF protection
- Password hashing with bcrypt
- Activity logging for audit trails
- Session management

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
