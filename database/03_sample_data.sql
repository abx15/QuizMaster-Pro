-- Insert Default Admin User (password: admin123)
INSERT IGNORE INTO users (username, email, password, role) 
VALUES (
    'admin', 
    'admin@quiz.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);

-- Insert Sample Questions
INSERT IGNORE INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES
('What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Personal Hypertext Processor', 'B'),
('Which symbol is used to denote the end of a PHP statement?', '.', ';', '!', ':', 'B'),
('What is the result of 8 % 3?', '2', '3', '1', '0', 'A'),
('Which function is used to redirect in PHP?', 'redirect()', 'location()', 'header()', 'goto()', 'C'),
('What does SQL stand for?', 'Structured Query Language', 'Simple Question Language', 'Structured Question Language', 'Simple Query Language', 'A'),
('Which of the following is a NoSQL database?', 'MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'C'),
('What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language', 'A'),
('Which CSS property is used to change the text color?', 'text-color', 'font-color', 'color', 'text-style', 'C'),
('What is the correct way to declare a JavaScript variable?', 'variable x;', 'var x;', 'let x;', 'Both B and C', 'D'),
('Which method converts JSON string to a JavaScript object?', 'JSON.parse()', 'JSON.stringify()', 'JSON.convert()', 'JSON.toObject()', 'A');