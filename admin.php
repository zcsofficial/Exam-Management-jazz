<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

if ($_SESSION['role'] != 'admin') {
    // If user is not an admin, redirect to the student dashboard
    header('Location: dashboard.php');
    exit();
}

// Get the logged-in user ID from session
$user_id = $_SESSION['user_id'];

// Fetch institution ID from logged-in user to manage students in their institution
$stmt = $pdo->prepare("SELECT institution_id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$institution_id = $user['institution_id'];

// Handle adding an exam
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_exam'])) {
    $exam_name = $_POST['exam_name'];
    $exam_date = $_POST['exam_date'];

    $stmt = $pdo->prepare("INSERT INTO exams (exam_name, exam_date, institution_id) VALUES (?, ?, ?)");
    $stmt->execute([$exam_name, $exam_date, $institution_id]);
}

// Fetch the list of students in the logged-in user's institution
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE institution_id = ? AND role = 'student'");
$stmt->execute([$institution_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all exams available
$stmt = $pdo->prepare("SELECT * FROM exams WHERE institution_id = ?");
$stmt->execute([$institution_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a question to an exam
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $exam_id = $_POST['exam_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_answer) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer]);
}

// Handle assigning an exam to students
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_exam'])) {
    $exam_id = $_POST['exam_id'];
    $students_to_assign = $_POST['students'];

    foreach ($students_to_assign as $student_id) {
        $stmt = $pdo->prepare("INSERT INTO exam_assignments (user_id, exam_id) VALUES (?, ?)");
        $stmt->execute([$student_id, $exam_id]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Exams</title>
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
            background-color: #F5F5F5; /* Soft Off-White background */
            color: #212121; /* Deep Charcoal text color */
        }

        /* Navbar Styles */
        .navbar {
            background-color: #2E004F; /* Dark Purple */
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
            color: #FFEB3B; /* Electric Yellow */
            font-weight: 500;
        }

        .navbar .nav-links a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-size: 16px;
        }

        .navbar .nav-links a:hover {
            color: #00BCD4; /* Neon Blue */
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

        .card-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background-color: #212121;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            background-color: #FFEB3B;
        }

        .card i {
            font-size: 40px;
            color: #00BCD4;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #fff;
        }

        .card p {
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
        .form-container select,
        .form-container textarea {
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-container {
                grid-template-columns: 1fr 1fr;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .nav-links {
                margin-top: 10px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .nav-links a {
                margin-left: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">Exam Management System</div>
        <div class="nav-links">
            <a href="admin.php">Home</a>
            <a href="admin.php">Manage Exams</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="results.php">Results</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Admin Dashboard -->
    <div class="dashboard-container">
        <h2>Welcome, Admin!</h2>

        <!-- Add Exam Form -->
        <div class="form-container">
            <h3>Add New Exam</h3>
            <form method="POST">
                <input type="text" name="exam_name" placeholder="Exam Name" required>
                <input type="date" name="exam_date" required>
                <button type="submit" name="add_exam">Add Exam</button>
            </form>
        </div>

        <!-- Add Question Form -->
        <div class="form-container">
            <h3>Add Question to Exam</h3>
            <form method="POST">
                <select name="exam_id" required>
                    <option value="">Select Exam</option>
                    <?php foreach ($exams as $exam) : ?>
                        <option value="<?= $exam['id'] ?>"><?= $exam['exam_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="question_text" placeholder="Enter question" required></textarea>
                <input type="text" name="option_a" placeholder="Option A" required>
                <input type="text" name="option_b" placeholder="Option B" required>
                <input type="text" name="option_c" placeholder="Option C" required>
                <input type="text" name="option_d" placeholder="Option D" required>
                <select name="correct_answer" required>
                    <option value="A">Option A</option>
                    <option value="B">Option B</option>
                    <option value="C">Option C</option>
                    <option value="D">Option D</option>
                </select>
                <button type="submit" name="add_question">Add Question</button>
            </form>
        </div>

        <!-- Assign Exam to Students -->
        <div class="form-container">
            <h3>Assign Exam to Students</h3>
            <form method="POST">
                <select name="exam_id" required>
                    <option value="">Select Exam</option>
                    <?php foreach ($exams as $exam) : ?>
                        <option value="<?= $exam['id'] ?>"><?= $exam['exam_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="students[]" multiple required>
                    <?php foreach ($students as $student) : ?>
                        <option value="<?= $student['id'] ?>"><?= $student['username'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="assign_exam">Assign Exam</button>
            </form>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Exam Management System</p>
    </div>

</body>

</html>
