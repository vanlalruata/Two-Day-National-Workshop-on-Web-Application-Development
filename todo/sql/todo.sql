CREATE DATABASE IF NOT EXISTS todo_app;
USE todo_app;

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  status ENUM('pending','done') DEFAULT 'pending',
  priority ENUM('low','medium','high') DEFAULT 'medium',
  due_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- sample data
INSERT INTO tasks (title, description, status, priority, due_date) VALUES
('Buy groceries', 'Milk, eggs, bread', 'pending','medium', NULL),
('Finish slides', 'Prepare workshop slides', 'pending','high', NULL);
