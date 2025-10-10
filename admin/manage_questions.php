<?php
include '../db.php';
include '../includes/header.php'; 

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $stmt = mysqli_prepare($conn, "DELETE FROM questions WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = "Question deleted successfully!";
    } else {
        $error = "Error deleting question: " . mysqli_error($conn);
    }
}

// Handle search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Build query
$query = "SELECT * FROM questions WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (question LIKE ? OR option_a LIKE ? OR option_b LIKE ? OR option_c LIKE ? OR option_d LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
    $types .= str_repeat('s', 5);
}

if (!empty($category) && $category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

$query .= " ORDER BY id DESC";

// Prepare and execute query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

$categories = [
    'all' => 'All Categories',
    'gk' => 'General Knowledge',
    'cs' => 'Computer Science', 
    'math' => 'Mathematics',
    'english' => 'English',
    'hindi' => 'Hindi'
];
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Questions</h1>
        <p class="text-gray-600">View, edit, and delete quiz questions</p>
    </div>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Questions</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Search questions or options...">
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <?php foreach($categories as $key => $name): ?>
                        <option value="<?php echo $key; ?>" <?php echo $category === $key ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="manage_questions.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors font-medium">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Questions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($questions)): ?>
            <div class="text-center py-12">
                <div class="text-5xl mb-4">📝</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Questions Found</h3>
                <p class="text-gray-500 mb-6">No questions match your search criteria.</p>
                <a href="add_question.php" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Question
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct Answer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($questions as $question): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?php echo $question['id']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 max-w-md truncate">
                                        <?php echo htmlspecialchars($question['question']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        A: <?php echo htmlspecialchars($question['option_a']); ?> | 
                                        B: <?php echo htmlspecialchars($question['option_b']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($categories[$question['category']] ?? $question['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold text-green-600"><?php echo $question['correct_answer']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- Edit Button -->
                                        <a href="edit_question.php?id=<?php echo $question['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                           title="Edit Question">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- View Button -->
                                        <button onclick="viewQuestion(<?php echo htmlspecialchars(json_encode($question)); ?>)" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <button onclick="confirmDelete(<?php echo $question['id']; ?>)" 
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Delete Question">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Results Count -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Showing <?php echo count($questions); ?> question(s)
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 flex justify-between items-center">
        <a href="dashboard.php" class="text-gray-600 hover:text-gray-800 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <a href="add_question.php" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
            <i class="fas fa-plus-circle mr-2"></i>Add New Question
        </a>
    </div>
</div>

<!-- View Question Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Question Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="questionDetails" class="space-y-4">
                <!-- Content will be loaded by JavaScript -->
            </div>
            
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewQuestion(question) {
    const categories = {
        'gk': 'General Knowledge',
        'cs': 'Computer Science',
        'math': 'Mathematics', 
        'english': 'English',
        'hindi': 'Hindi'
    };
    
    const details = `
        <div class="border-b pb-4">
            <h4 class="font-semibold text-gray-700 mb-2">Question:</h4>
            <p class="text-gray-900">${question.question}</p>
        </div>
        
        <div class="border-b pb-4">
            <h4 class="font-semibold text-gray-700 mb-2">Options:</h4>
            <div class="grid grid-cols-1 gap-2">
                <div class="flex items-center p-2 rounded ${question.correct_answer === 'A' ? 'bg-green-50 border border-green-200' : 'bg-gray-50'}">
                    <span class="font-semibold w-8">A:</span>
                    <span>${question.option_a}</span>
                    ${question.correct_answer === 'A' ? '<span class="ml-2 text-green-600"><i class="fas fa-check"></i> Correct</span>' : ''}
                </div>
                <div class="flex items-center p-2 rounded ${question.correct_answer === 'B' ? 'bg-green-50 border border-green-200' : 'bg-gray-50'}">
                    <span class="font-semibold w-8">B:</span>
                    <span>${question.option_b}</span>
                    ${question.correct_answer === 'B' ? '<span class="ml-2 text-green-600"><i class="fas fa-check"></i> Correct</span>' : ''}
                </div>
                <div class="flex items-center p-2 rounded ${question.correct_answer === 'C' ? 'bg-green-50 border border-green-200' : 'bg-gray-50'}">
                    <span class="font-semibold w-8">C:</span>
                    <span>${question.option_c}</span>
                    ${question.correct_answer === 'C' ? '<span class="ml-2 text-green-600"><i class="fas fa-check"></i> Correct</span>' : ''}
                </div>
                <div class="flex items-center p-2 rounded ${question.correct_answer === 'D' ? 'bg-green-50 border border-green-200' : 'bg-gray-50'}">
                    <span class="font-semibold w-8">D:</span>
                    <span>${question.option_d}</span>
                    ${question.correct_answer === 'D' ? '<span class="ml-2 text-green-600"><i class="fas fa-check"></i> Correct</span>' : ''}
                </div>
            </div>
        </div>
        
        <div class="border-b pb-4">
            <h4 class="font-semibold text-gray-700 mb-2">Details:</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium">Category:</span>
                    <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">${categories[question.category] || question.category}</span>
                </div>
                <div>
                    <span class="font-medium">Correct Answer:</span>
                    <span class="ml-2 font-semibold text-green-600">${question.correct_answer}</span>
                </div>
            </div>
        </div>
        
        ${question.explanation ? `
        <div>
            <h4 class="font-semibold text-gray-700 mb-2">Explanation:</h4>
            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">${question.explanation}</p>
        </div>
        ` : ''}
    `;
    
    document.getElementById('questionDetails').innerHTML = details;
    document.getElementById('viewModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

function confirmDelete(questionId) {
    if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
        window.location.href = `manage_questions.php?delete_id=${questionId}`;
    }
}

// Close modal when clicking outside
document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?> 