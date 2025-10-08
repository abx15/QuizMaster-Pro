-- Create Database
CREATE DATABASE IF NOT EXISTS quiz_app;
USE quiz_app;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions Table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    image_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin User
INSERT INTO users (username, email, password, role) 
VALUES (
    'admin', 
    'admin@quiz.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin'
) ON DUPLICATE KEY UPDATE username=username;

-- Insert Sample Questions
INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES
(
    'What does PHP stand for?',
    'Personal Home Page',
    'PHP: Hypertext Preprocessor',
    'Private Home Page', 
    'Personal Hypertext Processor',
    'B'
),
(
    'Which symbol is used to denote the end of a PHP statement?',
    '.',
    ';',
    '!',
    ':',
    'B'
),
(
    'What is the result of 8 % 3?',
    '2',
    '3',
    '1',
    '0',
    'A'
),
(
    'Which function is used to redirect in PHP?',
    'redirect()',
    'location()',
    'header()',
    'goto()',
    'C'
),
(
    'What does SQL stand for?',
    'Structured Query Language',
    'Simple Question Language',
    'Structured Question Language',
    'Simple Query Language',
    'A'
),
(
    'Which of the following is a NoSQL database?',
    'MySQL',
    'PostgreSQL',
    'MongoDB',
    'SQLite',
    'C'
),
(
    'What does HTML stand for?',
    'Hyper Text Markup Language',
    'High Tech Modern Language',
    'Hyper Transfer Markup Language',
    'Home Tool Markup Language',
    'A'
),
(
    'Which CSS property is used to change the text color?',
    'text-color',
    'font-color',
    'color',
    'text-style',
    'C'
),
(
    'What is the correct way to declare a JavaScript variable?',
    'variable x;',
    'var x;',
    'let x;',
    'Both B and C',
    'D'
),
(
    'Which method converts JSON string to a JavaScript object?',
    'JSON.parse()',
    'JSON.stringify()',
    'JSON.convert()',
    'JSON.toObject()',
    'A'
);