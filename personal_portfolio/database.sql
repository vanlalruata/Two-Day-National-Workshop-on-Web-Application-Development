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

-- portfolio_dummy_data.sql
-- Insert this after creating the database structure

-- 1. Insert Profile Data
INSERT INTO profile (full_name, title, bio, email, phone, location, profile_image, resume_file) 
VALUES (
    'Vanlaruata Hnamte', 
    'Full Stack Web Developer', 
    'Passionate full-stack developer with 5+ years of experience creating dynamic web applications. Specialized in PHP, JavaScript, and modern frameworks. Love turning complex problems into simple, beautiful designs.',
    'sarah.keimah@email.com',
    '+91 (987) 654-4567',
    'Mizoram, IN',
    'profile.jpg',
    'v_hnamte_cv.pdf'
);

-- 2. Insert Skills Data
INSERT INTO skills (skill_name, skill_level, category) VALUES
-- Programming Languages
('PHP', 90, 'Programming Languages'),
('JavaScript', 85, 'Programming Languages'),
('Python', 75, 'Programming Languages'),
('Java', 70, 'Programming Languages'),
('SQL', 88, 'Programming Languages'),

-- Frontend Technologies
('HTML5', 95, 'Frontend Technologies'),
('CSS3', 92, 'Frontend Technologies'),
('React', 80, 'Frontend Technologies'),
('Vue.js', 75, 'Frontend Technologies'),
('Bootstrap', 88, 'Frontend Technologies'),

-- Backend Technologies
('Laravel', 85, 'Backend Technologies'),
('Node.js', 78, 'Backend Technologies'),
('Express.js', 75, 'Backend Technologies'),
('MySQL', 90, 'Backend Technologies'),
('MongoDB', 70, 'Backend Technologies'),

-- Tools & Others
('Git', 88, 'Tools & Others'),
('Docker', 72, 'Tools & Others'),
('AWS', 68, 'Tools & Others'),
('Photoshop', 65, 'Tools & Others'),
('Agile/Scrum', 82, 'Tools & Others');

-- 3. Insert Projects Data
INSERT INTO projects (title, description, image_url, project_url, github_url, technologies, project_date, featured) VALUES
('E-Commerce Platform', 'A fully functional e-commerce website with shopping cart, user authentication, and payment integration. Built with Laravel and Vue.js.', 'ecommerce-platform.jpg', 'https://demo-ecommerce.example.com', 'https://github.com/vanlalruata/ecommerce-platform', 'Laravel, Vue.js, MySQL, Stripe API', '2023-11-15', 1),

('Task Management App', 'A collaborative task management application with real-time updates, team collaboration features, and progress tracking.', 'task-manager.jpg', 'https://taskflow.example.com', 'https://github.com/vanlalruata/task-manager', 'React, Node.js, Socket.io, MongoDB', '2023-08-22', 1),

('Portfolio Website', 'A responsive portfolio website with admin panel for content management. The very site you are viewing right now!', 'portfolio-website.jpg', 'https://hnamte.com', 'https://github.com/vanlalruata/portfolio', 'PHP, MySQL, JavaScript, CSS3', '2023-12-01', 0),

('Weather Dashboard', 'A beautiful weather dashboard that displays current conditions and forecasts for multiple cities with interactive charts.', 'weather-dashboard.jpg', 'https://weather-dash.example.com', 'https://github.com/vanlalruata/weather-dashboard', 'JavaScript, Chart.js, Weather API, Bootstrap', '2023-05-10', 0),

('Blog CMS', 'A custom content management system for bloggers with markdown support, categories, and SEO optimization.', 'blog-cms.jpg', 'https://blog-cms.example.com', 'https://github.com/vanlalruata/blog-cms', 'PHP, MySQL, JavaScript, Markdown', '2023-02-28', 0),

('Fitness Tracker', 'Mobile-first fitness tracking application with workout plans, progress analytics, and social features.', 'fitness-tracker.jpg', 'https://fit-track.example.com', 'https://github.com/vanlalruata/fitness-tracker', 'React Native, Firebase, Redux', '2022-11-15', 1);

-- 4. Insert Experiences Data
INSERT INTO experiences (job_title, company, location, start_date, end_date, current_job, description) VALUES
('Senior Full Stack Developer', 'TechInnovate Inc.', 'San Francisco, CA', '2022-03-01', NULL, 1, 'Lead development of web applications using modern technologies. Mentor junior developers and collaborate with cross-functional teams. Implemented CI/CD pipelines reducing deployment time by 60%.'),

('Web Developer', 'Digital Solutions LLC', 'San Francisco, CA', '2020-06-01', '2022-02-28', 0, 'Developed and maintained client websites and web applications. Worked with PHP, JavaScript, and various frameworks. Improved website performance by 40% through optimization techniques.'),

('Junior Web Developer', 'StartUp Ventures', 'San Francisco, CA', '2019-01-15', '2020-05-30', 0, 'Assisted in development of web applications and maintained existing codebase. Learned industry best practices and improved coding skills significantly.'),

('Web Development Intern', 'WebCraft Studio', 'San Francisco, CA', '2018-06-01', '2018-12-15', 0, 'Gained hands-on experience in web development. Assisted senior developers and contributed to real projects. Learned version control and team collaboration.');

-- 5. Insert Education Data
INSERT INTO education (degree, institution, location, start_date, end_date, current_study, description) VALUES
('Master of Computer Application', 'Annamalai University', 'Tamil Nadu, IN', '2021-09-01', '2023-05-30', 0, 'Specialized in Web Technologies and Software Engineering. GPA: 3.8/4.0. Thesis: "Optimizing Web Application Performance through Modern Caching Strategies"'),

('Bachelor of Computer Application', 'MCUJC', 'Bhopal, IN', '2017-09-01', '2021-05-30', 0, 'Focus on Web Development and Database Systems. Graduated Magna Cum Laude. Relevant coursework: Web Programming, Database Management, Algorithms, Software Engineering.'),

-- 6. Insert Contact Messages (Sample inquiries)
INSERT INTO contact_messages (name, email, subject, message, created_at, is_read) VALUES
('Vanlalruata Hnamte', 'vanlalruata@somewhere.com', 'Web Development Project Inquiry', 'Hello Sarah, I was impressed by your portfolio and would like to discuss a potential web development project for our company. Could we schedule a call next week?', '2023-12-10 14:30:00', 1),

('Emily Davis', 'emily.davis@techcorp.com', 'Senior Developer Position', 'Dear Sarah, We are looking for a senior developer at TechCorp and your profile matches our requirements perfectly. Would you be interested in exploring this opportunity?', '2023-12-08 09:15:00', 1),

('Michael Brown', 'michael.b@startup.io', 'Freelance Collaboration', 'Hi Sarah, I run a startup and need help with our web application. Your e-commerce project caught my attention. Are you available for freelance work?', '2023-12-05 16:45:00', 0),

('Jessica Wilson', 'jessica.wilson@designstudio.com', 'Partnership Opportunity', 'Hello, I am a UI/UX designer looking to partner with a skilled developer for client projects. Your work looks amazing! Let me know if you are interested.', '2023-12-03 11:20:00', 0),

('David Thompson', 'david.t@consulting.com', 'Technical Consultation', 'Hi Sarah, we need technical consultation for a Laravel project. Your expertise seems perfect for this. Are you available for consultation work?', '2023-12-01 13:10:00', 1);