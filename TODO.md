# TODO: Modify Valid ID Type on OCR Failure

## Information Gathered
- File: RIS/registrations/ris_registration_form.php
- Current behavior: When OCR fails to extract ID details, it only shows an error message ("OCR failed, please enter details manually").
- Requirement: Automatically set the valid ID type to "others" when OCR fails, and trigger the change event to show the "Specify other ID type" input field.

## Plan
- Edit the `processOCR` function in `ris_registration_form.php`.
- In the else block (when extractedData.idType and idNumber are not found), instead of just showing the error, set `idTypeSelect.value = 'other';` and dispatch the change event.
- This will automatically select "Other" and reveal the input field for specifying the other ID type.

## Dependent Files to be edited
- RIS/registrations/ris_registration_form.php

## Followup steps
- [x] Test the registration form by uploading an image that OCR cannot process (e.g., a non-ID image or corrupted image).
- [x] Verify that the valid ID type automatically changes to "Other" and the input field appears.
- [x] Ensure the form submission still validates correctly.
