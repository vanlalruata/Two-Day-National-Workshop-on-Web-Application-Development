# Project ER

```pgsql
+-----------------+       +-----------------+       +-----------------+
|     users       |       |     students    |       |     teachers    |
+-----------------+       +-----------------+       +-----------------+
| id (PK)         |<----->| id (PK)         |       | id (PK)         |
| username        |       | user_id (FK)    |       | user_id (FK)    |
| password        |       | roll_no         |       | emp_no          |
| role (admin/.. )|       | class_id (FK)   |       | subject_id (FK) |
| email           |       | dob             |       | phone           |
| profile_pic     |       | address         |       +-----------------+
| created_at      |       +-----------------+       
+-----------------+                

              +------------------+       +------------------+
              |     classes      |       |     subjects     |
              +------------------+       +------------------+
              | id (PK)          |       | id (PK)          |
              | name             |       | name             |
              | section          |       | code             |
              | year             |       +------------------+
              +------------------+                  

   +------------------+              +------------------+
   |   enrollments    |              |     teaches      |
   +------------------+              +------------------+
   | id (PK)          |              | id (PK)          |
   | student_id (FK)  |              | teacher_id (FK)  |
   | class_id (FK)    |              | class_id (FK)    |
   | subject_id (FK)  |              | subject_id (FK)  |
   +------------------+              +------------------+
