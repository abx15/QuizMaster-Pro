<?php
include 'db.php';
include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <header class="text-center mb-12">
        <div class="inline-block p-4 bg-white rounded-2xl shadow-lg mb-6">
            <!-- svg hai yaha pr  -->
            <svg class="w-16 h-16 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none" />
                <path d="M25 28 L29 32 L39 22" stroke="currentColor" stroke-width="3" fill="none" />
                <circle cx="25" cy="40" r="2" fill="currentColor" />
                <circle cx="32" cy="40" r="2" fill="currentColor" />
                <circle cx="39" cy="40" r="2" fill="currentColor" />
            </svg>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">QuizMaster Pro</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Test your knowledge with our interactive quiz platform</p>
    </header>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Welcome to QuizMaster!</h2>
                <p class="text-gray-600 mb-6 text-lg">
                    Challenge yourself with our carefully crafted quizzes covering various topics.
                    Whether you're preparing for exams or just having fun, we've got you covered.
                </p>
                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Interactive multiple-choice questions</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Instant feedback and scoring</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Detailed results analysis</span>
                    </div>
                </div>
            </div>
            <!-- quiz start karne ke liye  -->
            <div class="text-center">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Ready to Begin?</h3>
                    <p class="mb-6 opacity-90">Login or register to start your quiz journey</p>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <a href="quiz.php"
                            class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:scale-105 transform transition-transform duration-200 shadow-lg">
                            Take Quiz Now
                        </a>
                    <?php else: ?>
                        <a href="login.php"
                            class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:scale-105 transform transition-transform duration-200 shadow-lg">
                            Start Quiz Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-3xl font-bold text-indigo-600 mb-2">
            <!-- Questions Count -->
                <?php
                $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM questions");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $question_count = mysqli_fetch_assoc($result);
                echo $question_count['count'] . '+';
                ?>
            </div>
            <div class="text-gray-600">Questions Available</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-3xl font-bold text-indigo-600 mb-2">

            <!-- Active Users Count -->
                <?php
                $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user_count = mysqli_fetch_assoc($result);
                echo $user_count['count'] . '+';
                ?>
            </div>
            <div class="text-gray-600">Active Users</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-3xl font-bold text-indigo-600 mb-2">95%</div>
            <div class="text-gray-600">Satisfaction Rate</div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>