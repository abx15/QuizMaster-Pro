<?php
// result.php - Quiz Results
include 'db.php';

// Check if user is logged in and quiz was completed
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['quiz_started'])) {
    header('Location: quiz.php?new_quiz=true');
    exit();
}

// Get all questions
$stmt = mysqli_prepare($conn, "SELECT * FROM questions ORDER BY id");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

$total_questions = count($questions);
$score = $_SESSION['score'];
$percentage = round(($score / $total_questions) * 100);
$user_answers = $_SESSION['user_answers'];

// Reset quiz session
unset($_SESSION['current_question'], $_SESSION['quiz_started']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Quiz App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center text-indigo-600 hover:text-indigo-500">
                    <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none"/>
                        <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none"/>
                    </svg>
                    <span class="font-bold">QuizMaster</span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
            </div>
        </header>

        <!-- Results Summary -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 text-center">
            <div class="text-6xl mb-6">
                <?php if ($percentage >= 80): ?>🎉
                <?php elseif ($percentage >= 60): ?>👍
                <?php else: ?>😊
                <?php endif; ?>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Quiz Results</h1>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-green-50 rounded-xl p-6">
                    <div class="text-3xl font-bold text-green-600 mb-2"><?php echo $score; ?></div>
                    <div class="text-green-700 font-medium">Correct</div>
                </div>
                <div class="bg-red-50 rounded-xl p-6">
                    <div class="text-3xl font-bold text-red-600 mb-2"><?php echo $total_questions - $score; ?></div>
                    <div class="text-red-700 font-medium">Wrong</div>
                </div>
                <div class="bg-blue-50 rounded-xl p-6">
                    <div class="text-3xl font-bold text-blue-600 mb-2"><?php echo $total_questions; ?></div>
                    <div class="text-blue-700 font-medium">Total</div>
                </div>
                <div class="bg-purple-50 rounded-xl p-6">
                    <div class="text-3xl font-bold text-purple-600 mb-2"><?php echo $percentage; ?>%</div>
                    <div class="text-purple-700 font-medium">Score</div>
                </div>
            </div>

            <!-- Performance Message -->
            <div class="mb-8">
                <?php if ($percentage >= 80): ?>
                    <p class="text-xl text-green-600 font-semibold">Excellent! You're a quiz master! 🏆</p>
                <?php elseif ($percentage >= 60): ?>
                    <p class="text-xl text-blue-600 font-semibold">Good job! You're doing great! 👍</p>
                <?php else: ?>
                    <p class="text-xl text-orange-600 font-semibold">Keep practicing! You'll get better! 💪</p>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="quiz.php?new_quiz=true" 
                   class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition-colors duration-200">
                    Take Quiz Again
                </a>
                <a href="index.php" 
                   class="bg-gray-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-700 transition-colors duration-200">
                    Back to Home
                </a>
            </div>
        </div>

        <!-- Detailed Results -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Detailed Results</h2>
            <div class="space-y-6">
                <?php foreach ($questions as $index => $question): 
                    $user_answer = $user_answers[$question['id']] ?? 'Not answered';
                    $is_correct = $user_answer === $question['correct_answer'];
                    $options = [
                        'A' => $question['option_a'],
                        'B' => $question['option_b'],
                        'C' => $question['option_c'], 
                        'D' => $question['option_d']
                    ];
                ?>
                    <div class="border-2 <?php echo $is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'; ?> rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Question <?php echo $index + 1; ?></h3>
                            <div class="flex items-center">
                                <?php if ($is_correct): ?>
                                    <svg class="w-6 h-6 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                                        <circle cx="32" cy="32" r="30" stroke="#10b981" stroke-width="4" fill="none"/>
                                        <path d="M18 34 L28 44 L46 20" stroke="#10b981" stroke-width="4" fill="none"/>
                                    </svg>
                                    <span class="text-green-600 font-semibold">Correct</span>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                                        <circle cx="32" cy="32" r="30" stroke="#ef4444" stroke-width="4" fill="none"/>
                                        <path d="M20 20 L44 44 M44 20 L20 44" stroke="#ef4444" stroke-width="4" fill="none"/>
                                    </svg>
                                    <span class="text-red-600 font-semibold">Wrong</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-4 font-medium"><?php echo htmlspecialchars($question['question']); ?></p>
                        
                        <div class="grid gap-2">
                            <?php foreach ($options as $key => $value): 
                                $is_user_answer = $user_answer === $key;
                                $is_correct_answer = $question['correct_answer'] === $key;
                            ?>
                                <div class="flex items-center p-3 rounded-lg 
                                    <?php echo $is_correct_answer ? 'bg-green-100 border border-green-300' : 
                                          ($is_user_answer && !$is_correct ? 'bg-red-100 border border-red-300' : 'bg-gray-100'); ?>">
                                    <span class="w-6 h-6 flex items-center justify-center bg-white rounded mr-3 font-medium 
                                        <?php echo $is_correct_answer ? 'text-green-600' : 
                                              ($is_user_answer && !$is_correct ? 'text-red-600' : 'text-gray-600'); ?>">
                                        <?php echo $key; ?>
                                    </span>
                                    <span class="<?php echo $is_correct_answer ? 'text-green-800 font-medium' : 
                                                        ($is_user_answer && !$is_correct ? 'text-red-800 font-medium' : 'text-gray-700'); ?>">
                                        <?php echo htmlspecialchars($value); ?>
                                        <?php if ($is_correct_answer): ?>
                                            <span class="text-green-600 ml-2">✓ Correct</span>
                                        <?php elseif ($is_user_answer && !$is_correct): ?>
                                            <span class="text-red-600 ml-2">✗ Your answer</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>