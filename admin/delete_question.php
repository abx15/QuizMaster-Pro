<?php
// admin/delete_question.php - Delete Question
include '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$question_id = intval($_GET['id']);

// Get question details for confirmation message
$stmt = mysqli_prepare($conn, "SELECT question FROM questions WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $question_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$question = mysqli_fetch_assoc($result);

$error = '';
$success = '';

// Handle form submission for confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        // Delete the question
        $stmt = mysqli_prepare($conn, "DELETE FROM questions WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $question_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Question deleted successfully!';
            header('Refresh: 2; URL=dashboard.php');
        } else {
            $error = 'Failed to delete question. Please try again.';
        }
    } else {
        // User cancelled deletion
        header('Location: dashboard.php');
        exit();
    }
}

// If question doesn't exist, redirect to dashboard
if (!$question) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Question - Quiz App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="../index.php" class="flex items-center text-indigo-600 hover:text-indigo-500">
                        <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none"/>
                            <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none"/>
                        </svg>
                        <span class="font-bold">QuizMaster Admin</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-600 hover:text-gray-800">Dashboard</a>
                    <a href="../logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Delete Question</h1>
            <p class="text-red-600 mb-6">This action cannot be undone.</p>

            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($success); ?>
                    <p>Redirecting to dashboard...</p>
                </div>
            <?php else: ?>
                <!-- Confirmation -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Are you sure you want to delete this question?</h3>
                            <p class="text-red-700">This action will permanently remove the question from the database.</p>
                        </div>
                    </div>
                </div>

                <!-- Question Preview -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Question to be deleted:</h4>
                    <p class="text-gray-700"><?php echo htmlspecialchars($question['question']); ?></p>
                </div>

                <!-- Confirmation Form -->
                <form method="POST" class="flex gap-4">
                    <button type="submit" name="confirm_delete" 
                            class="bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        Yes, Delete Question
                    </button>
                    <a href="dashboard.php" 
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200">
                        Cancel
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>