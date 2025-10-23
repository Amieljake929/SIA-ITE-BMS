# TODO: Implement Rejection Notification and Archive for Resident Registrations

## Steps to Complete

- [x] Create `registration_archive` table with same columns as `registration` plus `remarks` (TEXT) and `rejected_at` (TIMESTAMP)
- [x] Create `send_rejection_email.php` function to send rejection emails with remarks and resubmission instructions
- [x] Create `ris_reject_form.php` page to input remarks before rejecting
- [x] Modify `ris_view_details.php` to link reject to the new form instead of direct action
- [x] Modify `ris_approve_reject.php` to handle rejection with remarks: move record to archive, send email, delete from registration
- [x] Update `ris_resident_registration.php` to exclude moved records (archived)
- [x] Test rejection flow: input remarks, send email, move to archive
- [x] Verify archived records are not shown in main list
- [x] Ensure images and data integrity during move
- [x] Add "View Archive" button to ris_resident_registration.php
- [x] Create ris_registration_archive.php page to display archived registrations
- [x] Create ris_archive_details.php page to view archived registration details
