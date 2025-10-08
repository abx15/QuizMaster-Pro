<?php
// admin/edit_question.php - Edit Question
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

// Define categories
$categories = [
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science',
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];

$question_id = intval($_GET['id']);
$error = '';
$success = '';

// Get current question data
$stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $question_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$question = mysqli_fetch_assoc($result);

if (!$question) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer'];
    $category = $_POST['category'];
    $explanation = trim($_POST['explanation']);
    $image_url = trim($_POST['image_url']);
    
    // Validation
    if (empty($question_text) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_answer) || empty($category)) {
        $error = 'All fields except image URL and explanation are required';
    } elseif (strlen($question_text) < 10) {
        $error = 'Question should be at least 10 characters long';
    } else {
        // Update the question
        $stmt = mysqli_prepare($conn, 
            "UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, category = ?, explanation = ?, image_url = ? 
             WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $category, $explanation, $image_url, $question_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Question updated successfully!';
            // Update local question data for form
            $question = [
                'question' => $question_text,
                'option_a' => $option_a,
                'option_b' => $option_b,
                'option_c' => $option_c,
                'option_d' => $option_d,
                'correct_answer' => $correct_answer,
                'category' => $category,
                'explanation' => $explanation,
                'image_url' => $image_url
            ];
        } else {
            $error = 'Failed to update question. Please try again.';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Edit Question</h1>
                <p class="text-gray-600">Update question details, category, and explanation</p>
            </div>
            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                ID: <?php echo $question_id; ?>
            </span>
        </div>

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

        <!-- Edit Question Form -->
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
                        <option value="<?= $key ?>" <?= ($question['category'] === $key) ? 'selected' : '' ?>>
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
                          placeholder="Enter your question here..."><?php echo htmlspecialchars($question['question']); ?></textarea>
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
                           value="<?php echo htmlspecialchars($question['option_a']); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option B <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_b" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option B"
                           value="<?php echo htmlspecialchars($question['option_b']); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option C <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_c" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option C"
                           value="<?php echo htmlspecialchars($question['option_c']); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option D <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="option_d" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Enter option D"
                           value="<?php echo htmlspecialchars($question['option_d']); ?>">
                </div>
            </div>

            <!-- Correct Answer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Correct Answer <span class="text-red-500">*</span>
                </label>
                <select name="correct_answer" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                    <option value="A" <?php echo $question['correct_answer'] === 'A' ? 'selected' : ''; ?>>Option A</option>
                    <option value="B" <?php echo $question['correct_answer'] === 'B' ? 'selected' : ''; ?>>Option B</option>
                    <option value="C" <?php echo $question['correct_answer'] === 'C' ? 'selected' : ''; ?>>Option C</option>
                    <option value="D" <?php echo $question['correct_answer'] === 'D' ? 'selected' : ''; ?>>Option D</option>
                </select>
            </div>

            <!-- Explanation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"
                          placeholder="Provide an explanation for the correct answer"><?php echo htmlspecialchars($question['explanation']); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">This helps users understand why the answer is correct</p>
            </div>

            <!-- Image URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image URL (Optional)</label>
                <input type="url" name="image_url"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                       placeholder="https://example.com/image.jpg"
                       value="<?php echo htmlspecialchars($question['image_url']); ?>">
                <p class="text-sm text-gray-500 mt-1">Provide a direct link to an image (jpg, png, gif)</p>
                
                <?php if ($question['image_url']): ?>
                    <div class="mt-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Image Preview:</p>
                        <img src="<?php echo htmlspecialchars($question['image_url']); ?>" 
                             alt="Question image" 
                             class="max-w-xs h-auto rounded-lg border border-gray-300"
                             onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-4">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Question
                </button>
                <a href="dashboard.php" 
                   class="bg-gray-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="delete_question.php?id=<?php echo $question_id; ?>" 
                   class="bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors duration-200 flex items-center ml-auto"
                   onclick="return confirm('Are you sure you want to delete this question?')">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </a>
            </div>
        </form>

        <!-- Question Metadata -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-700 mb-3">Question Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Created:</span> 
                    <?php echo date('F j, Y g:i A', strtotime($question['created_at'])); ?>
                </div>
                <div>
                    <span class="font-medium">Question ID:</span> 
                    <?php echo $question_id; ?>
                </div>
                <div>
                    <span class="font-medium">Character Count:</span> 
                    <?php echo strlen($question['question']); ?>
                </div>
                <div>
                    <span class="font-medium">Has Image:</span> 
                    <?php echo $question['image_url'] ? 'Yes' : 'No'; ?>
                </div>
                <div>
                    <span class="font-medium">Has Explanation:</span> 
                    <?php echo !empty($question['explanation']) ? 'Yes' : 'No'; ?>
                </div>
                <div>
                    <span class="font-medium">Category:</span> 
                    <span class="font-semibold text-indigo-600">
                        <?php echo htmlspecialchars($categories[$question['category']] ?? $question['category']); ?>
                    </span>
                </div>
            </div>
        </div>
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

// Image URL validation and preview
const imageUrlInput = document.querySelector('input[name="image_url"]');
const imagePreview = document.createElement('div');
imagePreview.className = 'mt-3 hidden';
imagePreview.innerHTML = '<p class="text-sm font-medium text-gray-700 mb-2">New Image Preview:</p><img class="max-w-xs h-auto rounded-lg border border-gray-300">';
imageUrlInput.parentNode.appendChild(imagePreview);

imageUrlInput.addEventListener('input', function() {
    const url = this.value.trim();
    if (url) {
        const img = imagePreview.querySelector('img');
        img.src = url;
        img.onload = function() {
            imagePreview.classList.remove('hidden');
        };
        img.onerror = function() {
            imagePreview.classList.add('hidden');
        };
    } else {
        imagePreview.classList.add('hidden');
    }
});
</script>

<?php include '../includes/footer.php'; ?>