-- Create dedicated database user (optional)
CREATE USER IF NOT EXISTS 'quiz_user'@'localhost' IDENTIFIED BY 'quiz_password123';
GRANT ALL PRIVILEGES ON quiz_app.* TO 'quiz_user'@'localhost';
FLUSH PRIVILEGES;