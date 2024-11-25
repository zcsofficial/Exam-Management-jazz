<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student details (optional)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch assigned exams for the student
$stmt = $pdo->prepare("SELECT exams.* FROM exams
                       JOIN exam_assignments ON exams.id = exam_assignments.exam_id
                       WHERE exam_assignments.user_id = ?");
$stmt->execute([$user_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has already attended an exam
$attended_exams = [];
$stmt = $pdo->prepare("SELECT exam_id FROM exam_attendance WHERE user_id = ?");
$stmt->execute([$user_id]);
$attended_exams_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($attended_exams_result as $attended_exam) {
    $attended_exams[] = $attended_exam['exam_id'];
}

// Handle exam start
if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];

    // Check if the user has already attended the exam
    if (in_array($exam_id, $attended_exams)) {
        echo "You have already attended this exam.";
        exit();
    }

    // Fetch exam details
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exam) {
        // Fetch questions for this exam
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
        $stmt->execute([$exam_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Store exam session details
        $_SESSION['exam_id'] = $exam_id;
        $_SESSION['questions'] = $questions;
        $_SESSION['current_question'] = 0;

        // Insert a record into exam_attendance to mark this exam as attended
        $stmt = $pdo->prepare("INSERT INTO exam_attendance (user_id, exam_id, attended_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $exam_id]);

        header('Location: take_exam.php'); // Redirect to the exam page where the student can start the exam
        exit();
    } else {
        // If exam not found
        echo "Exam not found.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Exams - Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #F5F5F5;
            color: #212121;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #2E004F;
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar .logo {
            font-size: 24px;
            color: #FFEB3B;
            font-weight: 500;
        }

        .navbar .nav-links a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-size: 16px;
        }

        .navbar .nav-links a:hover {
            color: #00BCD4;
        }

        /* Exam Container */
        .exam-container {
            padding: 30px;
            max-width: 1200px;
            margin: 50px auto;
        }

        h2 {
            font-size: 30px;
            color: #2E004F;
            margin-bottom: 20px;
        }

        .exam-card {
            background-color: #212121;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
            margin-bottom: 15px;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            background-color: #FFEB3B;
        }

        .exam-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #fff;
        }

        .exam-card p {
            font-size: 16px;
            color: #ddd;
        }

        .exam-card button {
            background-color: #00BCD4;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .exam-card button:hover {
            background-color: #0097A7;
        }

        .exam-card .attended {
            background-color: #FFEB3B;
            color: #212121;
            cursor: not-allowed;
        }

        /* Footer Styles */
        .footer {
            background-color: #212121;
            color: #FFEB3B;
            text-align: center;
            padding: 10px 0;
            margin-top: 40px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">Exam Management System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="exam.php">Available Exams</a>
            <a href="student_dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Exam List -->
    <div class="exam-container">
        <h2>Assigned Exams</h2>

        <!-- Display Exams -->
        <?php if ($exams): ?>
            <?php foreach ($exams as $exam): ?>
                <div class="exam-card">
                    <h3><?= $exam['exam_name'] ?></h3>
                    <p>Exam Date: <?= $exam['exam_date'] ?></p>
                    <?php if (in_array($exam['id'], $attended_exams)): ?>
                        <button class="attended" disabled>Already Attended</button>
                    <?php else: ?>
                        <button onclick="window.location.href='exam.php?exam_id=<?= $exam['id'] ?>'">Start Exam</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No exams assigned at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Exam Management System. All Rights Reserved.</p>
    </div>

</body>

</html>
