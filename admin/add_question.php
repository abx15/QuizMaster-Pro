<?php
include '../db.php';
include '../includes/header.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$categories = [
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science', 
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
    $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
    $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
    $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
    $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $explanation = mysqli_real_escape_string($conn, $_POST['explanation']);

    $stmt = mysqli_prepare($conn, "INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, category, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssss", $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $category, $explanation);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Question added successfully!";
    } else {
        $error = "Error adding question: " . mysqli_error($conn);
    }
}
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Add New Question</h1>
        <p class="text-gray-600">Create a new question for the quiz</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $success; ?>
                <a href="add_question.php" class="ml-4 text-green-800 font-semibold hover:text-green-900">Add Another</a>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Category</option>
                    <?php foreach($categories as $key => $name): ?>
                        <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Question -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                <textarea name="question" required rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter your question here..."></textarea>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Option A</label>
                    <input type="text" name="option_a" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Option A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Option B</label>
                    <input type="text" name="option_b" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Option B">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Option C</label>
                    <input type="text" name="option_c" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Option C">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Option D</label>
                    <input type="text" name="option_d" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Option D">
                </div>
            </div>

            <!-- Correct Answer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                <select name="correct_answer" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Correct Answer</option>
                    <option value="A">Option A</option>
                    <option value="B">Option B</option>
                    <option value="C">Option C</option>
                    <option value="D">Option D</option>
                </select>
            </div>

            <!-- Explanation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Explain why this answer is correct..."></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                    <i class="fas fa-plus-circle mr-2"></i>Add Question
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>