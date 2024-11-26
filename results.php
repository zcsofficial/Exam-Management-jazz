<?php
// Include the database connection
include 'db.php';

// Start the session
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Check if the user is an admin or a student
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $institution_id = $_SESSION['institution_id']; // Assumes institution_id is stored in session

    // Query based on user role
    if ($role === 'admin') {
        // Admin can see results for all students in the institution
        $query = "SELECT 
                    er.id,
                    u.username,
                    u.email,
                    e.exam_name,
                    er.correct_answers,
                    er.total_questions,
                    er.percentage,
                    er.status,
                    er.created_at,
                    u.institution_name
                  FROM exam_results er
                  INNER JOIN users u ON er.user_id = u.id
                  INNER JOIN exams e ON er.exam_id = e.id
                  WHERE u.institution_id = :institution_id
                  ORDER BY er.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR);
    } else {
        // Student can see only their own results
        $query = "SELECT 
                    er.id,
                    u.username,
                    u.email,
                    e.exam_name,
                    er.correct_answers,
                    er.total_questions,
                    er.percentage,
                    er.status,
                    er.created_at,
                    u.institution_name
                  FROM exam_results er
                  INNER JOIN users u ON er.user_id = u.id
                  INNER JOIN exams e ON er.exam_id = e.id
                  WHERE er.user_id = :user_id
                  ORDER BY er.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    }

    // Execute the query
    $stmt->execute();

    // Fetch results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching results: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results - Admin</title>
    <!-- External CSS and Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5F5;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background-color: #2E004F;
        }
        .navbar-brand, .nav-link {
            color: #FFEB3B !important;
        }
        .table thead {
            background-color: #2E004F;
            color: #FFEB3B;
        }
        .table tbody tr:nth-child(even) {
            background-color: #EEE;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #FFF;
        }
        .btn-primary {
            background-color: #00BCD4;
            border-color: #00BCD4;
        }
        .btn-primary:hover {
            background-color: #008C9E;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Exam Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    
                    <li class="nav-item"><a class="nav-link" href="admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Results Table -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">
            <?php echo ($role === 'admin') ? 'All Students Exam Results' : 'Your Exam Results'; ?>
        </h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Institution</th>
                    <th>Exam Name</th>
                    <th>Correct Answers</th>
                    <th>Total Questions</th>
                    <th>Percentage</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?= htmlspecialchars($result['id']); ?></td>
                            <td><?= htmlspecialchars($result['username']); ?></td>
                            <td><?= htmlspecialchars($result['email']); ?></td>
                            <td><?= htmlspecialchars($result['institution_name']); ?></td>
                            <td><?= htmlspecialchars($result['exam_name']); ?></td>
                            <td><?= htmlspecialchars($result['correct_answers']); ?></td>
                            <td><?= htmlspecialchars($result['total_questions']); ?></td>
                            <td><?= htmlspecialchars($result['percentage']); ?>%</td>
                            <td><?= htmlspecialchars($result['status']); ?></td>
                            <td><?= htmlspecialchars($result['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No results found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
