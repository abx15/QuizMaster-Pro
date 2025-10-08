<?php
// register.php - User Registration
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if username or email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = 'Username or email already exists';
        } else {
            // Insert new user (plain password)
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Registration successful! Redirecting to login...';
                header('Refresh: 2; URL=login.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Quiz App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-indigo-100 rounded-xl mb-4">
                    <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none"/>
                        <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none"/>
                        <circle cx="25" cy="40" r="2" fill="currentColor"/>
                        <circle cx="32" cy="40" r="2" fill="currentColor"/>
                        <circle cx="39" cy="40" r="2" fill="currentColor"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
                <p class="text-gray-600">Join QuizMaster today</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                </div>
                
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                    Create Account
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
