# TODO: Remove Specified Columns from Resident Registration Table

## Tasks
- [x] Update table header (thead): Remove th elements for Address, Phone, Email, Employment, Senior, PWD, Solo Parent, Voter, Student, Indigenous. Keep Created and Actions.
- [x] Update table body (tbody): Remove corresponding td elements for removed columns, keep Created and Actions.
- [x] Update no data row: Change colspan to 10.
- [x] Update search input: Change placeholder to "Search by name..." and remove data-email, data-phone, data-address attributes from tr elements.
- [x] Update filterTable script: Remove email, phone, address checks in matchesSearch.

## Dependent Files
- RIS/RIS_admin/ris_resident_registration.php

## Followup Steps
- [x] Verify the table displays correctly with the remaining columns.
- [x] Test search functionality (now only by name).
- [x] Ensure no errors in PHP or JavaScript.
