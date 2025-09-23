# Structure

```language
school_mgmt/
├─ config/
│  └─ db.php
├─ inc/
│  ├─ header.php
│  ├─ footer.php
│  ├─ auth_check.php
│  └─ functions.php
├─ auth/
│  ├─ login.php
│  └─ logout.php
├─ dashboard.php
├─ students/
│  ├─ list.php
│  ├─ add.php
│  ├─ edit.php
│  └─ delete.php
├─ teachers/  (same pattern)
├─ classes/   (same pattern)
├─ subjects/  (same pattern)
├─ enrollments/ (same pattern)
├─ attendance/ (same pattern)
├─ exams/ (same pattern)
├─ results/ (same pattern)
└─ assets/
   └─ (bootstrap via CDN; optional CSS)
