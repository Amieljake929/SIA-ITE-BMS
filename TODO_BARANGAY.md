# TODO: Implement MFA for Barangay Login (Official, Staff, BPSO)

## Overview
Add Multi-Factor Authentication (MFA) for barangay officials, staff, and BPSO in the login process. After successful password verification, generate a 6-digit random code, store it in the database with a 30-minute expiration, send it via email, and redirect to MFA verification page instead of directly to the dashboard.

## Steps to Complete
- [x] Modify `login_process_BARANGAY.php` to handle MFA for barangay roles:
  - After password and approval check, if role is 'official', 'staff', or 'bpso', generate 6-digit code.
  - Insert code into `barangay_mfa` table with expiration (NOW() + 30 minutes).
  - Send email with the code using PHPMailer.
  - Set temporary session variables for MFA.
  - Redirect to `mfa_verify.php` instead of dashboard.
- [x] Ensure `phpmailer_config.php` is included for email sending.
- [x] Test the flow: Login -> Email sent -> Verify code -> Dashboard.
- [x] Handle code expiration: Codes auto-expire after 30 minutes (cleanup in `mfa_verify_process.php`).
- [x] Verify database table `barangay_mfa` exists with columns: id (AUTO_INCREMENT), barangay_id (INT), code (VARCHAR(6)), verified (TINYINT DEFAULT 0), expires_at (DATETIME).

## Dependent Files
- `BMS/login/login_process_BARANGAY.php` (primary changes)
- `BMS/login/mfa_verify.php` (already exists, used for verification)
- `BMS/login/mfa_verify_process.php` (already exists, handles verification)
- `BMS/phpmailer_config.php` (for sending emails)
- `BMS/login/db_connect.php` (database connection)

## Followup Steps
- Test email sending (ensure PHPMailer is configured correctly).
- Test code expiration (wait 30+ minutes or manually set time).
- Ensure no breaking changes to existing login for other roles.
- If table `barangay_mfa` doesn't exist, create it via SQL.
