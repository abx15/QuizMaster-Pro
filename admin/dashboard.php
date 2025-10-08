<?php
include '../db.php';
include '../includes/header.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get stats
$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
$questions_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM questions"))['count'];
$categories_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT category) as count FROM questions"))['count'];
$recent_users = mysqli_fetch_all(mysqli_query($conn, "SELECT username, email, created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5"), MYSQLI_ASSOC);
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600">Welcome back, <?php echo $_SESSION['username']; ?>!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $users_count; ?></h3>
                    <p class="text-gray-600">Total Users</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-question-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $questions_count; ?></h3>
                    <p class="text-gray-600">Total Questions</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-lg border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="fas fa-folder text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $categories_count; ?></h3>
                    <p class="text-gray-600">Categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="space-y-4">
                <a href="add_question.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white mr-4">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Add New Question</h3>
                        <p class="text-sm text-gray-600">Create a new quiz question</p>
                    </div>
                </a>
                <a href="manage_questions.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white mr-4">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Manage Questions</h3>
                        <p class="text-sm text-gray-600">Edit or delete existing questions</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Users</h2>
            <div class="space-y-3">
                <?php foreach($recent_users as $user): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800"><?php echo $user['username']; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $user['email']; ?></p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>