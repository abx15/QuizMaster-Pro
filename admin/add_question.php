<?php
// admin/add_question.php - Add Question
include '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Define categories
$categories = [
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science',
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer'];
    $category = $_POST['category'];
    $explanation = trim($_POST['explanation']);
    $image_url = trim($_POST['image_url']);
    
    // Validation
    if (empty($question) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_answer) || empty($category)) {
        $error = 'All fields except image URL and explanation are required';
    } elseif (strlen($question) < 10) {
        $error = 'Question should be at least 10 characters long';
    } else {
        // Prepare and execute insert statement
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, category, explanation, image_url) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssssss", $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $category, $explanation, $image_url);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Question added successfully!';
            // Clear form
            $_POST = array();
        } else {
            $error = 'Failed to add question. Please try again.';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Add New Question</h1>
        <p class="text-gray-600 mb-6">Create a new quiz question with category and explanation</p>

        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Question Form -->
        <form method="POST" class="space-y-6">
            <!-- Category Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $key => $name): ?>
                        <option value="<?= $key ?>" <?= (isset($_POST['category']) && $_POST['category'] === $key) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Question Text -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Question Text <span class="text-red-500">*</span>
                </label>
                <textarea name="question" required rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"
                          placeholder="Enter your question here..."><?php echo isset($_POST['question']) ? htmlspecialchars($_POST['question']) : ''; ?></textarea>
                <p class="text-sm text-gray-500 mt-1">Minimum 10 characters</p>
            </div>

            <!-- Options Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option A <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_a" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option A"
                           value="<?php echo isset($_POST['option_a']) ? htmlspecialchars($_POST['option_a']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option B <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_b" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option B"
                           value="<?php echo isset($_POST['option_b']) ? htmlspecialchars($_POST['option_b']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option C <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_c" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option C"
                           value="<?php echo isset($_POST['option_c']) ? htmlspecialchars($_POST['option_c']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option D <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_d" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option D"
                           value="<?php echo isset($_POST['option_d']) ? htmlspecialchars($_POST['option_d']) : ''; ?>">
                </div>
            </div>

            <!-- Correct Answer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Correct Answer <span class="text-red-500">*</span>
                </label>
                <select name="correct_answer" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                    <option value="">Select correct answer</option>
                    <option value="A" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] === 'A') ? 'selected' : ''; ?>>Option A</option>
                    <option value="B" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] === 'B') ? 'selected' : ''; ?>>Option B</option>
                    <option value="C" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] === 'C') ? 'selected' : ''; ?>>Option C</option>
                    <option value="D" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] === 'D') ? 'selected' : ''; ?>>Option D</option>
                </select>
            </div>

            <!-- Explanation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"
                          placeholder="Provide an explanation for the correct answer (this will be shown to users after they answer)"><?php echo isset($_POST['explanation']) ? htmlspecialchars($_POST['explanation']) : ''; ?></textarea>
                <p class="text-sm text-gray-500 mt-1">This helps users understand why the answer is correct</p>
            </div>

            <!-- Image URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image URL (Optional)</label>
                <input type="url" name="image_url"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                       placeholder="https://example.com/image.jpg"
                       value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>">
                <p class="text-sm text-gray-500 mt-1">Provide a direct link to an image (jpg, png, gif)</p>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-4">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Question
                </button>
                <a href="dashboard.php" 
                   class="bg-gray-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const question = document.querySelector('textarea[name="question"]').value.trim();
    const category = document.querySelector('select[name="category"]').value;
    const correctAnswer = document.querySelector('select[name="correct_answer"]').value;
    
    if (question.length < 10) {
        e.preventDefault();
        alert('Question must be at least 10 characters long.');
        return;
    }
    
    if (!category) {
        e.preventDefault();
        alert('Please select a category.');
        return;
    }
    
    if (!correctAnswer) {
        e.preventDefault();
        alert('Please select the correct answer.');
        return;
    }
});

// Character counter for question
const questionTextarea = document.querySelector('textarea[name="question"]');
const questionCounter = document.createElement('div');
questionCounter.className = 'text-sm text-gray-500 text-right mt-1';
questionTextarea.parentNode.appendChild(questionCounter);

function updateCounter() {
    const length = questionTextarea.value.length;
    questionCounter.textContent = `${length} characters`;
    if (length < 10) {
        questionCounter.className = 'text-sm text-red-500 text-right mt-1';
    } else {
        questionCounter.className = 'text-sm text-green-500 text-right mt-1';
    }
}

questionTextarea.addEventListener('input', updateCounter);
updateCounter();
</script>

<?php include '../includes/footer.php'; ?>