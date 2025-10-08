<?php
// admin/dashboard.php - Admin Dashboard
include '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all questions
$stmt = mysqli_prepare($conn, "SELECT * FROM questions ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quiz App</title>
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
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="add_question.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        Add Question
                    </a>
                    <a href="../logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-2xl font-bold text-indigo-600 mb-2"><?php echo count($questions); ?></div>
                <div class="text-gray-600">Total Questions</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-2xl font-bold text-green-600 mb-2">
                    <?php 
                    $user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"));
                    echo $user_count['count'];
                    ?>
                </div>
                <div class="text-gray-600">Total Users</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-2xl font-bold text-blue-600 mb-2">
                    <?php 
                    $admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='admin'"));
                    echo $admin_count['count'];
                    ?>
                </div>
                <div class="text-gray-600">Admins</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-2xl font-bold text-purple-600 mb-2">
                    <?php echo date('M j, Y'); ?>
                </div>
                <div class="text-gray-600">Today</div>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Manage Questions</h2>
            </div>
            
            <?php if (empty($questions)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500 text-lg">No questions found. <a href="add_question.php" class="text-indigo-600 hover:text-indigo-500">Add your first question</a></p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct Answer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($questions as $question): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                            <?php echo htmlspecialchars($question['question']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <?php echo htmlspecialchars($question['correct_answer']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($question['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="edit_question.php?id=<?php echo $question['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <a href="delete_question.php?id=<?php echo $question['id']; ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>