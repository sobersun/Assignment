## Setup Script

### `setup.sh`
- **Description:** Bash script for setting up the Laravel project.
- **Usage:** Run `bash ./setup.sh` in the terminal.
- **Tasks:** Sets permissions, installs Composer dependencies, generates application key, runs migrations, seeds the database, clears cache, and optimizes autoloader.

## Seeder

### `AdminSeeder`
- **Description:** Seeder for creating admin accounts and other initial data.
- **Usage:** Run as part of the setup process or manually using `php artisan db:seed --class=AdminSeeder`.

## Command to Generate Users and Commission

### Generate Users and Commission
- **Command:** `php artisan generate:commission-and-users {referringUserId}`
- **Description:** Generates users with commissions for the specified referring user.
- **Usage:** Replace `{referringUserId}` with the ID of the user who has the referral code.
- **Example:** `php artisan generate:commission-and-users 1` (where `1` is the referring user's ID)

# Project API Endpoints
### Register
- **Endpoint:** `/api/register`
- **Method:** POST
- **Description:** Allows users to register.
- **Request Body:**
  - `name`: User's name
  - `email`: User's email
  - `password`: User's password
  - `referral_code`: referral_code if any
### Login
- **Endpoint:** `/api/login`
- **Method:** POST
- **Description:** Allows users to log in.
- **Request Body:**
  - `email`: User's email
  - `password`: User's password
- **Response:** User authentication token

## Referrals

### Get Referrals
- **Endpoint:** `/api/referrals`
- **Method:** GET
- **Description:** Retrieve referrals for the authenticated user.
- **Authorization:** Bearer Token (sanctum)
- **Response:** List of user's referrals with additional details.

## Earning

### Get Earning
- **Endpoint:** `/api/earning`
- **Method:** GET
- **Description:** Retrieve earning details for the authenticated user.
- **Authorization:** Bearer Token (sanctum)
- **Response:** Earning details such as total earnings, commission percentage, etc.

