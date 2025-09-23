# Project Structure

```language
/school_mgmt/
│── config/
│   └── db.php         # Database connection
│
│── auth/
│   ├── login.php
│   ├── logout.php
│
│── dashboard.php      # Main dashboard
│── profile.php        # User settings/profile
│
│── students/
│   ├── students.php   # List students
│   ├── add_student.php
│   ├── edit_student.php
│
│── teachers/
│   ├── teachers.php
│   ├── add_teacher.php
│   ├── edit_teacher.php
│
│── classes/
│   └── classes.php
│
│── subjects/
│   └── subjects.php
│
│── index.php          # Landing page → redirect to login
