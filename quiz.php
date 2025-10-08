<?php
include 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Define categories
$categories = [
    'all' => 'All Categories',
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science',
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];

// Get selected category
$selected_category = $_GET['category'] ?? 'all';

// Reset quiz when category changes or new quiz starts
if (isset($_GET['new_quiz']) || !isset($_SESSION['current_question']) || $_SESSION['selected_category'] !== $selected_category) {
    $_SESSION['current_question'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['user_answers'] = [];
    $_SESSION['quiz_started'] = true;
    $_SESSION['selected_category'] = $selected_category;
    $_SESSION['question_feedback'] = [];
}

// Build query based on category
if ($selected_category === 'all') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions ORDER BY RAND()");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE category = ? ORDER BY RAND()");
    mysqli_stmt_bind_param($stmt, "s", $selected_category);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

$total_questions = count($questions);
$current_index = $_SESSION['current_question'];
$current_question = $questions[$current_index] ?? null;
$show_feedback = false;
$feedback_message = '';
$is_correct = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $selected_answer = $_POST['answer'];
    $question_id = $_POST['question_id'];

    $_SESSION['user_answers'][$question_id] = $selected_answer;

    // Check if answer is correct
    if ($selected_answer === $current_question['correct_answer']) {
        $_SESSION['score']++;
        $is_correct = true;
        $feedback_message = 'Correct! 🎉';
    } else {
        $is_correct = false;
        $feedback_message = 'Incorrect! 😔';
    }

    // Store feedback for this question
    $_SESSION['question_feedback'][$question_id] = [
        'selected' => $selected_answer,
        'correct' => $current_question['correct_answer'],
        'explanation' => $current_question['explanation'] ?? '',
        'is_correct' => $is_correct
    ];

    $show_feedback = true;
}
?>

<?php include 'includes/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Category Selection -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Select Quiz Category</h2>
        <div class="flex flex-wrap gap-3">
            <?php foreach ($categories as $key => $name): ?>
                <a href="quiz.php?category=<?= $key ?>&new_quiz=true" 
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 <?= $selected_category === $key ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    <?= htmlspecialchars($name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Progress and Stats -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    <?= htmlspecialchars($categories[$selected_category]) ?> - 
                    Question <?= $current_index + 1 ?> of <?= $total_questions ?>
                </h2>
                <p class="text-gray-600 text-sm mt-1">
                    Category: <span class="font-semibold text-indigo-600"><?= htmlspecialchars($categories[$selected_category]) ?></span>
                </p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600"><?= $_SESSION['score'] ?></div>
                    <div class="text-sm text-gray-600">Score</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        <?= $total_questions > 0 ? round(($_SESSION['score'] / ($current_index + 1)) * 100) : 0 ?>%
                    </div>
                    <div class="text-sm text-gray-600">Accuracy</div>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500 ease-out" 
                 style="width: <?= $total_questions > 0 ? (($current_index + 1) / $total_questions) * 100 : 0 ?>%"></div>
        </div>
    </div>

    <?php if ($total_questions === 0): ?>
        <!-- No Questions Available -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="text-6xl mb-4">📚</div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">No Questions Available</h2>
            <p class="text-xl text-gray-600 mb-6">There are no questions in the selected category yet.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="quiz.php?category=all" 
                   class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition-colors duration-200">
                    Try All Categories
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/add_question.php" 
                       class="bg-green-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-colors duration-200">
                        Add Questions
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($current_index < $total_questions && $current_question): ?>
        <!-- Current Question -->
        <div class="bg-white rounded-2xl shadow-xl p-8 transition-all duration-300 transform hover:shadow-2xl">
            <!-- Question Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex-1"><?= htmlspecialchars($current_question['question']) ?></h3>
                <?php if ($current_question['category']): ?>
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium ml-4 whitespace-nowrap">
                        <?= htmlspecialchars($categories[$current_question['category']] ?? $current_question['category']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Question Image (if available) -->
            <?php if (!empty($current_question['image_url'])): ?>
                <div class="mb-6 text-center">
                    <img src="<?= htmlspecialchars($current_question['image_url']) ?>" 
                         alt="Question image" 
                         class="max-w-full h-auto rounded-lg mx-auto shadow-md max-h-64 object-contain"
                         onerror="this.style.display='none'">
                </div>
            <?php endif; ?>

            <!-- Options Form -->
            <form method="POST" class="space-y-4" id="quiz-form">
                <input type="hidden" name="question_id" value="<?= $current_question['id'] ?>">

                <?php
                $options = [
                    'A' => $current_question['option_a'],
                    'B' => $current_question['option_b'],
                    'C' => $current_question['option_c'],
                    'D' => $current_question['option_d']
                ];
                
                foreach ($options as $key => $value):
                    $is_selected = $show_feedback && isset($_POST['answer']) && $_POST['answer'] === $key;
                    $is_correct_answer = $show_feedback && $key === $current_question['correct_answer'];
                    
                    $classes = "p-4 border-2 rounded-xl transition-all duration-300 cursor-pointer group ";
                    
                    if ($show_feedback) {
                        if ($is_correct_answer) {
                            $classes .= "border-green-500 bg-green-50 shadow-green-100 shadow-lg scale-105";
                        } elseif ($is_selected && !$is_correct) {
                            $classes .= "border-red-500 bg-red-50 shadow-red-100 shadow-lg";
                        } else {
                            $classes .= "border-gray-200 bg-gray-50 opacity-75";
                        }
                    } else {
                        $classes .= "border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md transform hover:scale-[1.02]";
                    }
                ?>
                    <label class="block">
                        <input type="radio" name="answer" value="<?= $key ?>" class="hidden" 
                               <?= $show_feedback ? 'disabled' : '' ?>>
                        <div class="<?= $classes ?>">
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg mr-4 font-semibold transition-colors duration-200
                                    <?= $show_feedback ? 
                                        ($is_correct_answer ? 'bg-green-500 text-white shadow-green-400 shadow-inner' : 
                                         ($is_selected ? 'bg-red-500 text-white shadow-red-400 shadow-inner' : 'bg-gray-200 text-gray-600')) 
                                        : 'bg-indigo-100 text-indigo-600 group-hover:bg-indigo-500 group-hover:text-white' ?>">
                                    <?= $key ?>
                                </div>
                                <span class="text-gray-700 font-medium flex-1"><?= htmlspecialchars($value) ?></span>
                                
                                <?php if ($show_feedback): ?>
                                    <?php if ($is_correct_answer): ?>
                                        <svg class="w-6 h-6 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php elseif ($is_selected && !$is_correct): ?>
                                        <svg class="w-6 h-6 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>

                <!-- Feedback Section -->
                <?php if ($show_feedback): ?>
                    <div class="mt-6 p-4 rounded-lg border-2 <?= $is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' ?>">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <?php if ($is_correct): ?>
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold <?= $is_correct ? 'text-green-800' : 'text-red-800' ?>">
                                    <?= $feedback_message ?>
                                </h4>
                                <?php if (!$is_correct): ?>
                                    <p class="text-gray-700 mt-1">
                                        Correct answer: <span class="font-semibold text-green-600"><?= $current_question['correct_answer'] ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($current_question['explanation'])): ?>
                                    <div class="mt-3 p-3 bg-white rounded-lg border">
                                        <h5 class="font-semibold text-gray-800 mb-2">Explanation:</h5>
                                        <p class="text-gray-700"><?= htmlspecialchars($current_question['explanation']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Submit/Next Button -->
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-[1.02] disabled:scale-100 mt-6 flex items-center justify-center"
                        <?= $show_feedback ? 'disabled' : '' ?>
                        id="submit-btn">
                    <?php if ($show_feedback): ?>
                        <span>Next Question</span>
                        <svg class="w-5 h-5 ml-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    <?php else: ?>
                        <span>Submit Answer</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    <?php endif; ?>
                </button>
            </form>

            <?php if ($show_feedback): ?>
                <script>
                    setTimeout(() => {
                        window.location.href = 'quiz.php?category=<?= $selected_category ?>';
                    }, 3000);
                </script>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Quiz Completed -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Quiz Completed!</h2>
            <p class="text-xl text-gray-600 mb-2">You've answered all <?= $total_questions ?> questions.</p>
            <p class="text-lg text-indigo-600 font-semibold mb-6">
                Final Score: <?= $_SESSION['score'] ?>/<?= $total_questions ?> 
                (<?= $total_questions > 0 ? round(($_SESSION['score'] / $total_questions) * 100) : 0 ?>%)
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="result.php" 
                   class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition-colors duration-200 transform hover:scale-105">
                    View Detailed Results
                </a>
                <a href="quiz.php?category=<?= $selected_category ?>&new_quiz=true" 
                   class="bg-green-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-colors duration-200 transform hover:scale-105">
                    Restart Quiz
                </a>
                <a href="index.php" 
                   class="bg-gray-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-700 transition-colors duration-200 transform hover:scale-105">
                    Back to Home
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Enhanced form validation and UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quiz-form');
    const submitBtn = document.getElementById('submit-btn');
    const options = document.querySelectorAll('input[name="answer"]');
    
    // Enable/disable submit button based on selection
    options.forEach(option => {
        option.addEventListener('change', function() {
            submitBtn.disabled = false;
        });
    });
    
    // Add loading state to form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!document.querySelector('input[name="answer"]:checked')) {
                e.preventDefault();
                alert('Please select an answer before submitting.');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Processing...</span><svg class="w-5 h-5 ml-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"></path></svg>';
        });
    }
    
    // Add hover effects to options
    const labels = document.querySelectorAll('label');
    labels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            if (!document.querySelector('input[name="answer"]:checked')) {
                this.querySelector('div').classList.add('shadow-md', 'scale-[1.02]');
            }
        });
        
        label.addEventListener('mouseleave', function() {
            this.querySelector('div').classList.remove('shadow-md', 'scale-[1.02]');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>