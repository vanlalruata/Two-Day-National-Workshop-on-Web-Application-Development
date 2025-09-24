CREATE DATABASE portfolio_db;
USE portfolio_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    title VARCHAR(100) NOT NULL,
    bio TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    location VARCHAR(100),
    profile_image VARCHAR(255),
    resume_file VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    skill_level INT NOT NULL CHECK (skill_level >= 0 AND skill_level <= 100),
    category VARCHAR(50)
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    technologies VARCHAR(255),
    project_date DATE,
    featured BOOLEAN DEFAULT FALSE
);

CREATE TABLE experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_title VARCHAR(100) NOT NULL,
    company VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE,
    current_job BOOLEAN DEFAULT FALSE,
    description TEXT
);

CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    degree VARCHAR(100) NOT NULL,
    institution VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE,
    current_study BOOLEAN DEFAULT FALSE,
    description TEXT
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE
);

-- Insert default admin user (password: Admin@123)
INSERT INTO users (username, password, email) 
VALUES ('admin', '$2y$10$KF6.y2p.nw7IQ8zlSBjL2Osfdjm4j.MZGaTR0A41L1hSK.CsMmPu2', 'admin@portfolio.com');