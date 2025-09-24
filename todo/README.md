# Simple Todo App (No Tailwind, Simple CSS)

## Overview
A small Todo application built with PHP + MySQL. Uses minimal, professional CSS (no frameworks). Supports CRUD for tasks: create, read, update, delete.

## Files
- `index.php` - list tasks
- `add.php` - create a task
- `edit.php` - edit a task (title, description, priority, due date, status)
- `delete.php` - delete a task
- `config/db.php` - database connection (update credentials)
- `inc/header.php` / `inc/footer.php` - page chrome
- `assets/css/styles.css` - minimal styles
- `sql/todo.sql` - SQL to create database, table, sample rows

## Setup
1. Import SQL:
   - Using phpMyAdmin or mysql CLI: `mysql -u root -p < sql/todo.sql`
2. Update DB credentials in `config/db.php`.
3. Place the `todo_app_simple_css` folder inside your webserver root (e.g. `htdocs`).
4. Open: `http://localhost/todo_app/index.php`.

## Notes
- Uses prepared statements for safety.
- No authentication; add if you need multi-user separation.
- For production, add CSRF tokens, additional input validation, and escaping as needed.
