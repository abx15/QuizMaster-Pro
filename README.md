# QuizMaster Pro 🎯
**Complete Interactive Quiz Web Application**  
Built with **Procedural PHP**, **MySQL**, **Tailwind CSS**, and **JavaScript**.

---

## 🚀 Features

### 👥 User Features
- User Registration & Login (Secure authentication)
- Category-based Quizzes: GK, CS, Math, English, Hindi
- Interactive Quiz Interface with real-time feedback
- Detailed Results with explanations per question
- Progress Tracking with visual score bar

### 🛠️ Admin Features
- Admin Dashboard to manage questions & categories
- Full CRUD: Add, Edit, Delete Questions
- Add explanations and images for questions
- View quiz statistics

### 🎯 Quiz Features
- Multiple categories + "All Categories"
- Instant feedback for answers
- Randomized questions each attempt
- Score analytics and accuracy percentage
- Learning-focused explanations

---

## 📋 Prerequisites
- **Web Server:** XAMPP, WAMP, LAMP, or any PHP-supported server
- **PHP:** Version 7.4+
- **MySQL:** Version 5.7+
- **Browser:** Modern browser with JavaScript enabled

---

## 🛠️ Installation

### Step 1: Download & Setup
1. Clone or download the repository:
```bash
git clone https://github.com/abx15/QuizMaster-Pro.git

```
### Step 2: Database Setup

#### Option A: Automatic Setup
1. Start your server and MySQL.  
2. Open in browser:  
http://localhost/quiz-app/setup_database.php

3. Follow instructions for automatic setup with sample data.

#### Option B: Manual Setup
1. Create database `quiz_app`.  
2. Import SQL file or run:
```sql
CREATE DATABASE quiz_app;
USE quiz_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    explanation TEXT NULL,
    image_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
---
## 🔑 Default Admin Account
- **Username:** admin  
- **Password:** admin123  

**Sample Questions:** Already included in database.

---

## 📁 Project Structure
```text
quiz-app/
├── index.php
├── login.php
├── register.php
├── logout.php
├── quiz.php
├── result.php
├── db.php
├── setup_database.php
├── includes/
│ ├── header.php
│ └── footer.php
├── admin/
│ ├── dashboard.php
│ ├── add_question.php
│ ├── edit_question.php
│ └── delete_question.php
└── README.md
```


---

## 🎮 How to Use

### Users
- Register/Login  
- Select Quiz Category  
- Take Quiz  
- View Results & Explanations  

### Admins
- Login as admin  
- Access Dashboard  
- Manage Questions & Categories  
- View Analytics

---

## 🛡️ Security Features
- Password hashing (`password_hash()`)  
- Prepared statements for SQL injection prevention  
- Session management & regeneration  
- Input sanitization (`htmlspecialchars()`)  
- Role-based access control

---

## 🎨 UI/UX Features
- Fully responsive design  
- Tailwind CSS styling  
- Smooth animations & hover effects  
- Progress indicators

---

## 🔧 Customization
Add new categories by editing:
```php
$categories = [
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science',
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];

```
## 🐛 Troubleshooting

- **DB Errors:** Check MySQL service & `db.php` credentials  
- **Session Issues:** Ensure `session_start()` is called  
- **Pages Not Loading:** Verify PHP installation & file paths  
- **Admin Access Denied:** Check user role in database  

**Debug Mode:**  
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```



## 🌐 Browser Compatibility

- Chrome  
- Firefox  
- Safari  
- Edge (Latest versions)

---

## 📦 Quick Start

1. Clone repo to `htdocs/quiz-app/`  
2. Start XAMPP/WAMP (Apache + MySQL)  
3. Open in browser: `http://localhost/quiz-app/setup_database.php`  
4. Login as **admin/admin123**  
5. Start quizzing! 🎉

---

## 📝 License

MIT License — Open Source  
Built with ❤️ using **PHP**, **MySQL**, **Tailwind CSS**, and **JavaScript**  
Built and maintained by **Arun Kumar Bind**
