<?php
// db.php - Database connection
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'quiz_app';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Create tables if they don't exist
function create_tables($conn) {
    // Users table
    $users_table = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user','admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Questions table
    $questions_table = "CREATE TABLE IF NOT EXISTS questions (
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
    
    mysqli_query($conn, $users_table);
    mysqli_query($conn, $questions_table);
    
    // Insert sample admin user if not exists
    $admin_check = "SELECT id FROM users WHERE username = 'admin'";
    $result = mysqli_query($conn, $admin_check);
    
    if (mysqli_num_rows($result) == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (username, email, password, role) 
                        VALUES ('admin', 'admin@quiz.com', '$admin_password', 'admin')";
        mysqli_query($conn, $insert_admin);
    }
    
    // Insert sample questions if none exist
    $question_check = "SELECT id FROM questions LIMIT 1";
    $result = mysqli_query($conn, $question_check);
    
    if (mysqli_num_rows($result) == 0) {
        $sample_questions = [
            [
                "What does PHP stand for?",
                "Personal Home Page",
                "PHP: Hypertext Preprocessor", 
                "Private Home Page",
                "Personal Hypertext Processor",
                "B"
            ],
            [
                "Which symbol is used to denote the end of a PHP statement?",
                ".",
                ";",
                "!",
                ":",
                "B"
            ],
            [
                "What is the result of 8 % 3?",
                "2",
                "3", 
                "1",
                "0",
                "A"
            ],
            [
                "Which function is used to redirect in PHP?",
                "redirect()",
                "location()",
                "header()",
                "goto()",
                "C"
            ],
            [
                "What does SQL stand for?",
                "Structured Query Language",
                "Simple Question Language",
                "Structured Question Language", 
                "Simple Query Language",
                "A"
            ]
        ];
        
        foreach ($sample_questions as $q) {
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) 
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $q[0], $q[1], $q[2], $q[3], $q[4], $q[5]);
            mysqli_stmt_execute($stmt);
        }
    }
}

create_tables($conn);
?>