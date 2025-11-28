# ADBS-BRMS Project Documentation

## Overview
ADBS-BRMS is a Barangay Record Management System built with Laravel 12. It is designed to streamline the management of barangay residents, households, certificates, and official records. The system includes role-based access control, activity logging, and backup capabilities.

## Key Features

### 1. Certificate Management
- **Request System:** Residents can request certificates.
- **Processing:** Officials can approve or reject requests and update statuses.
- **Generation:** Automated PDF generation for certificates.
- **Fee Management:** Admins can configure certificate fees.

### 2. Resident & Household Management
- **Database:** Comprehensive CRUD operations for Residents and Households.
- **Records:** Management of bulk resident and household records, including template downloads and file uploads.
- **Account Management:** dedicated management for Resident and Official accounts.

### 3. Account Verification
- **Registration:** Public registration with proof of identity submission.
- **Verification:** Officials can review, approve, or reject registration requests.
- **Proof Handling:** Secure storage and retrieval of identity proofs.

### 4. System Administration
- **Activity Logs:** Detailed tracking of user actions for audit trails.
- **Backups:** Built-in system for creating, downloading, and restoring database backups.
- **Analytics:** Dashboard with summary analytics.

## User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Guest** | Login, Register. |
| **User** | Access Dashboard, Manage Profile, Request Certificates. |
| **Clerk** | Manage Residents, Households, Accounts, Records, Verifications, Backups, Activity Logs. |
| **Admin** | All Clerk permissions + Manage Certificate Fees. |

## Technical Stack

- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Frontend:** Blade Templates with Vite
- **Database:** MySQL (implied)
- **PDF Generation:** `barryvdh/laravel-dompdf`
- **Testing:** PHPUnit

## Project Structure Highlights

- **`app/Models`**: Core data models (Resident, Household, CertificateRequest, etc.).
- **`app/Http/Controllers`**: Logic for handling requests (CertificateRequestController, ResidentController, etc.).
- **`routes/web.php`**: Defines all web routes and middleware groups.
- **`resources/views`**: Frontend Blade templates.
- **`database/migrations`**: Database schema definitions.
