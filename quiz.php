<?php
include 'db.php';

if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Categories
$categories = ['all'=>'All Categories','gk'=>'General Knowledge','cs'=>'Computer Science','math'=>'Mathematics','english'=>'English','hindi'=>'Hindi'];
$selected_category = $_GET['category'] ?? 'all';

// Reset quiz if new or category changed
if (isset($_GET['new_quiz']) || !isset($_SESSION['current_question']) || $_SESSION['selected_category'] !== $selected_category) {
    $_SESSION['current_question'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['user_answers'] = [];
    $_SESSION['question_feedback'] = [];
    $_SESSION['selected_category'] = $selected_category;
}

// Fetch questions
if ($selected_category === 'all') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions ORDER BY id ASC");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE category=? ORDER BY id ASC");
    mysqli_stmt_bind_param($stmt, "s", $selected_category);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

$total_questions = count($questions);
$current_index = $_SESSION['current_question'] ?? 0;
$current_question = $questions[$current_index] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['answer']) && $current_question) {
    $answer = $_POST['answer'];
    $qid = $current_question['id'];

    $_SESSION['user_answers'][$qid] = $answer;

    if ($answer === $current_question['correct_answer']) {
        $_SESSION['score']++;
        $_SESSION['question_feedback'][$qid] = [
            'correct' => true,
            'selected' => $answer,
            'explanation' => $current_question['explanation'] ?? ''
        ];
    } else {
        $_SESSION['question_feedback'][$qid] = [
            'correct' => false,
            'selected' => $answer,
            'explanation' => $current_question['explanation'] ?? ''
        ];
    }

    // Reload to show feedback
    header("Location: quiz.php?category=$selected_category");
    exit();
}

// Navigation
if (isset($_GET['action'])) {
    if ($_GET['action']==='next' && $current_index<$total_questions-1) {
        $_SESSION['current_question']++;
        header("Location: quiz.php?category=$selected_category");
        exit();
    } elseif ($_GET['action']==='prev' && $current_index>0) {
        $_SESSION['current_question']--;
        header("Location: quiz.php?category=$selected_category");
        exit();
    }
}
?>

<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/quiz.css">

<div class="quiz-container">
    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <!-- Quiz Stats -->
        <div class="quiz-stats glass-card mb-6">
            <div class="flex justify-between items-center">
                <div class="text-lg font-semibold text-gray-800">
                    Question <?= $current_index + 1 ?> of <?= $total_questions ?>
                </div>
                <div class="text-lg font-semibold text-gray-800">
                    Score: <?= $_SESSION['score'] ?? 0 ?>/<?= $total_questions ?>
                </div>
            </div>
            <div class="progress-bar mt-3">
                <div class="progress-fill" style="width: <?= $total_questions > 0 ? (($current_index + 1) / $total_questions) * 100 : 0 ?>%"></div>
            </div>
        </div>

        <!-- Category Selection -->
        <div class="glass-card p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Select Quiz Category</h2>
            <div class="flex flex-wrap gap-3">
                <?php foreach ($categories as $key=>$name): ?>
                    <a href="quiz.php?category=<?= $key ?>&new_quiz=true"
                       class="category-btn px-5 py-3 rounded-xl font-medium transition <?= $selected_category===$key?'active':'bg-gray-100 text-gray-700' ?>">
                       <?= htmlspecialchars($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($total_questions===0): ?>
            <div class="glass-card p-12 text-center">
                <div class="text-5xl mb-4">📚</div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">No Questions Available</h2>
                <p class="text-gray-600 mb-6">There are no questions available for the selected category.</p>
                <a href="quiz.php?category=all" class="btn-primary inline-block">Browse All Categories</a>
            </div>
        <?php elseif ($current_question): ?>
        
        <div class="glass-card p-8 mb-6">
            <!-- Question -->
            <div class="mb-8">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        Q
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight"><?= htmlspecialchars($current_question['question']) ?></h3>
                </div>
            </div>

            <!-- Options -->
            <form method="POST">
                <input type="hidden" name="question_id" value="<?= $current_question['id'] ?>">
                <?php 
                $options=['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d'];
                $feedback = $_SESSION['question_feedback'][$current_question['id']] ?? null;

                foreach($options as $key=>$field):
                    $value = $current_question[$field];
                    $is_selected = ($feedback['selected'] ?? '') === $key;
                    $is_correct_answer = $current_question['correct_answer'] === $key;
                    $is_wrong_selected = isset($feedback['correct']) && !$feedback['correct'] && $is_selected;
                    
                    $option_class = 'option-card';
                    if ($is_selected) $option_class .= ' selected';
                    if (isset($feedback)) {
                        if ($is_correct_answer) $option_class .= ' correct';
                        if ($is_wrong_selected) $option_class .= ' incorrect';
                    }
                ?>
                    <label class="block mb-4 cursor-pointer">
                        <input type="radio" name="answer" value="<?= $key ?>" class="hidden peer" 
                               <?= $is_selected?'checked':'' ?> <?= isset($feedback)?'disabled':'' ?>>
                        <div class="<?= $option_class ?> p-5 rounded-xl transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 font-semibold text-gray-600 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">
                                    <?= $key ?>
                                </div>
                                <span class="text-lg text-gray-800"><?= htmlspecialchars($value) ?></span>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>

                <?php if (!isset($feedback)): ?>
                    <div class="text-center mt-8">
                        <button type="submit" class="btn-primary text-lg px-12 py-4">
                            Submit Answer
                        </button>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Feedback and Explanation -->
            <?php if ($feedback): ?>
                <div class="mt-8">
                    <div class="p-5 rounded-xl mb-4 <?= $feedback['correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?>">
                        <div class="flex items-center gap-3">
                            <?php if ($feedback['correct']): ?>
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white">
                                    ✓
                                </div>
                                <div>
                                    <h4 class="font-bold text-green-800 text-lg">Correct! Well done.</h4>
                                </div>
                            <?php else: ?>
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white">
                                    ✗
                                </div>
                                <div>
                                    <h4 class="font-bold text-red-800 text-lg">Incorrect Answer</h4>
                                    <p class="text-red-700 mt-1">
                                        Correct Answer: <?= htmlspecialchars($current_question['correct_answer'] . '. ' . $current_question['option_' . strtolower($current_question['correct_answer'])]); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Explanation Section -->
                    <?php if (!empty($feedback['explanation'])): ?>
                        <div class="explanation-box p-5 rounded-xl">
                            <h4 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                                <span>💡</span> Explanation
                            </h4>
                            <p class="text-gray-700 leading-relaxed"><?= htmlspecialchars($feedback['explanation']) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="explanation-box p-5 rounded-xl">
                            <h4 class="font-bold text-gray-800 text-lg mb-3 flex items-center gap-2">
                                <span>💡</span> Explanation
                            </h4>
                            <p class="text-gray-600 italic">No explanation available for this question.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Navigation -->
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <?php if ($current_index>0): ?>
                    <a href="quiz.php?category=<?= $selected_category ?>&action=prev" class="btn-secondary">
                        ← Previous
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <?php if ($current_index<$total_questions-1): ?>
                    <a href="quiz.php?category=<?= $selected_category ?>&action=next" class="btn-primary">
                        Next Question →
                    </a>
                <?php else: ?>
                    <a href="result.php" class="btn-primary bg-gradient-to-r from-green-500 to-green-600">
                        View Final Results ✓
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="glass-card p-12 text-center">
            <div class="text-6xl mb-6">🎉</div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Quiz Completed!</h2>
            <p class="text-xl text-gray-600 mb-8">You've answered all the questions in this quiz.</p>
            <a href="result.php" class="btn-primary text-lg px-12 py-4 inline-block">
                View Detailed Results
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="assets/js/quiz.js"></script>

<?php include 'includes/footer.php'; ?>