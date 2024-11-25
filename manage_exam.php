<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

if ($_SESSION['role'] != 'student') {
    // If user is not an admin, redirect to the dashboard
    header('Location: dashboard.php');
    exit();
}

// Get the logged-in user ID from session
$user_id = $_SESSION['user_id'];

// Fetch institution ID from logged-in user to manage exams in their institution
$stmt = $pdo->prepare("SELECT institution_id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$institution_id = $user['institution_id'];

// Fetch all exams available in the institution
$stmt = $pdo->prepare("SELECT * FROM exams WHERE institution_id = ?");
$stmt->execute([$institution_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle exam update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_exam'])) {
    $exam_id = $_POST['exam_id'];
    $exam_name = $_POST['exam_name'];
    $exam_date = $_POST['exam_date'];

    $stmt = $pdo->prepare("UPDATE exams SET exam_name = ?, exam_date = ? WHERE id = ?");
    $stmt->execute([$exam_name, $exam_date, $exam_id]);

    header('Location: manage_exam.php'); // Redirect back to the same page to avoid re-posting data
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - Admin Dashboard</title>
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

        /* Dashboard Container */
        .dashboard-container {
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

        /* Form Styles */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #00BCD4;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #0097A7;
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
            <a href="admin.php">Manage Exams</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Admin Dashboard -->
    <div class="dashboard-container">
        <h2>Manage Exams</h2>

        <!-- Exam List -->
        <div class="card-container">
            <?php foreach ($exams as $exam) : ?>
                <div class="exam-card">
                    <h3><?= $exam['exam_name'] ?></h3>
                    <p><?= $exam['exam_date'] ?></p>
                    <button class="edit-button" onclick="editExam(<?= $exam['id'] ?>, '<?= $exam['exam_name'] ?>', '<?= $exam['exam_date'] ?>')">Edit</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Edit Exam Form -->
        <div class="form-container" id="edit-exam-form" style="display: none;">
            <h3>Edit Exam</h3>
            <form method="POST">
                <input type="hidden" name="exam_id" id="exam_id">
                <input type="text" name="exam_name" id="exam_name" placeholder="Exam Name" required>
                <input type="date" name="exam_date" id="exam_date" required>
                <button type="submit" name="update_exam">Update Exam</button>
            </form>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Exam Management System. All Rights Reserved.</p>
    </div>

    <script>
        function editExam(id, name, date) {
            document.getElementById('edit-exam-form').style.display = 'block';
            document.getElementById('exam_id').value = id;
            document.getElementById('exam_name').value = name;
            document.getElementById('exam_date').value = date;
        }
    </script>

</body>

</html>
