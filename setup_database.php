<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'quiz_app';

try {
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists<br>";
    } else {
        die("Error creating database: " . $conn->error);
    }
    
    // Select database
    $conn->select_db($database);
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user','admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully<br>";
    } else {
        die("Error creating users table: " . $conn->error);
    }
    
    // Create questions table
    $sql = "CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        option_a VARCHAR(255) NOT NULL,
        option_b VARCHAR(255) NOT NULL,
        option_c VARCHAR(255) NOT NULL,
        option_d VARCHAR(255) NOT NULL,
        correct_answer ENUM('A','B','C','D') NOT NULL,
        image_url VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Questions table created successfully<br>";
    } else {
        die("Error creating questions table: " . $conn->error);
    }
    
    // Insert admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT IGNORE INTO users (username, email, password, role) 
            VALUES ('admin', 'admin@quiz.com', '$admin_password', 'admin')";
    
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo "Admin user created successfully<br>";
        } else {
            echo "Admin user already exists<br>";
        }
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
    
    // Insert sample questions
    $sample_questions = [
        "('What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Personal Hypertext Processor', 'B')",
        "('Which symbol is used to denote the end of a PHP statement?', '.', ';', '!', ':', 'B')",
        "('What is the result of 8 % 3?', '2', '3', '1', '0', 'A')",
        "('Which function is used to redirect in PHP?', 'redirect()', 'location()', 'header()', 'goto()', 'C')",
        "('What does SQL stand for?', 'Structured Query Language', 'Simple Question Language', 'Structured Question Language', 'Simple Query Language', 'A')",
        "('Which of the following is a NoSQL database?', 'MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'C')",
        "('What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language', 'A')",
        "('Which CSS property is used to change the text color?', 'text-color', 'font-color', 'color', 'text-style', 'C')",
        "('What is the correct way to declare a JavaScript variable?', 'variable x;', 'var x;', 'let x;', 'Both B and C', 'D')",
        "('Which method converts JSON string to a JavaScript object?', 'JSON.parse()', 'JSON.stringify()', 'JSON.convert()', 'JSON.toObject()', 'A')"
    ];
    
    $questions_inserted = 0;
    foreach ($sample_questions as $question) {
        $sql = "INSERT IGNORE INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES $question";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $questions_inserted++;
        }
    }
    
    echo "Inserted $questions_inserted sample questions<br>";
    echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
    echo "<p>You can now <a href='index.php'>access the application</a></p>";
    echo "<p><strong>Admin Login:</strong> username: 'admin', password: 'admin123'</p>";
    
} catch (Exception $e) {
    die("Database setup failed: " . $e->getMessage());
}

$conn->close();
?>