<?php
// includes/header.php - Common Header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine user role and navigation items
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$is_admin = $is_logged_in && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = $is_logged_in ? $_SESSION['username'] : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        $page_titles = [
            'index.php' => 'QuizMaster Pro - Home',
            'login.php' => 'Login - Quiz App',
            'register.php' => 'Register - Quiz App',
            'quiz.php' => 'Quiz - Quiz App',
            'result.php' => 'Results - Quiz App',
            'dashboard.php' => 'Admin Dashboard - Quiz App',
            'add_question.php' => 'Add Question - Quiz App',
            'edit_question.php' => 'Edit Question - Quiz App',
            'delete_question.php' => 'Delete Question - Quiz App'
        ];
        echo $page_titles[$current_page] ?? 'QuizMaster Pro';
        ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
        .nav-link.active {
            color: #4f46e5;
            font-weight: 600;
        }
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #4f46e5;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex flex-col">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-3 text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                        <div class="p-2 bg-indigo-100 rounded-xl">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                                <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none"/>
                                <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none"/>
                                <circle cx="25" cy="40" r="2" fill="currentColor"/>
                                <circle cx="32" cy="40" r="2" fill="currentColor"/>
                                <circle cx="39" cy="40" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">QuizMaster Pro</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <?php if (!$is_logged_in): ?>
                        <!-- Public Navigation -->
                        <a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                        <a href="login.php" class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="register.php" class="nav-link <?php echo $current_page === 'register.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    <?php elseif ($is_admin): ?>
                        <!-- Admin Navigation -->
                        <a href="admin/dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="admin/add_question.php" class="nav-link <?php echo $current_page === 'add_question.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-plus-circle mr-2"></i>Add Question
                        </a>
                        <a href="index.php" class="nav-link text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                    <?php else: ?>
                        <!-- User Navigation -->
                        <a href="quiz.php" class="nav-link <?php echo $current_page === 'quiz.php' ? 'active' : ''; ?> text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-play-circle mr-2"></i>Take Quiz
                        </a>
                        <a href="index.php" class="nav-link text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                    <?php endif; ?>
                </div>

                <!-- User Info & Mobile Menu -->
                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <!-- User Welcome & Logout -->
                        <div class="hidden md:flex items-center space-x-4">
                            <span class="text-gray-600">
                                <i class="fas fa-user-circle mr-2"></i>Welcome, <span class="font-semibold text-indigo-600"><?php echo htmlspecialchars($username); ?></span>
                            </span>
                            <?php if ($is_admin): ?>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-crown mr-1"></i>Admin
                                </span>
                            <?php endif; ?>
                            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-4">
                <?php if (!$is_logged_in): ?>
                    <!-- Public Mobile Navigation -->
                    <a href="index.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'index.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <a href="login.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'login.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-sign-in-alt mr-3"></i>Login
                    </a>
                    <a href="register.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'register.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-user-plus mr-3"></i>Register
                    </a>
                <?php elseif ($is_admin): ?>
                    <!-- Admin Mobile Navigation -->
                    <div class="px-4 py-2 text-sm text-gray-500 font-semibold">Admin Panel</div>
                    <a href="admin/dashboard.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'dashboard.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="admin/add_question.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'add_question.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-plus-circle mr-3"></i>Add Question
                    </a>
                    <a href="index.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <div class="px-4 py-2 text-sm text-gray-500">Logged in as: <span class="font-semibold text-indigo-600"><?php echo htmlspecialchars($username); ?></span></div>
                        <a href="logout.php" class="block py-3 px-4 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-3"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <!-- User Mobile Navigation -->
                    <a href="quiz.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200 <?php echo $current_page === 'quiz.php' ? 'bg-indigo-50 text-indigo-600 font-semibold' : ''; ?>">
                        <i class="fas fa-play-circle mr-3"></i>Take Quiz
                    </a>
                    <a href="index.php" class="block py-3 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors duration-200">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <div class="px-4 py-2 text-sm text-gray-500">Welcome, <span class="font-semibold text-indigo-600"><?php echo htmlspecialchars($username); ?></span></div>
                        <a href="logout.php" class="block py-3 px-4 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-3"></i>Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow"></main>