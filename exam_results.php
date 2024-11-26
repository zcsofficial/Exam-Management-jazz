<?php
session_start();
require_once 'db.php';

// Check if the user is logged in and has completed an exam
if (!isset($_SESSION['user_id']) || !isset($_SESSION['exam_id']) || !isset($_SESSION['answers'])) {
    header('Location: login.php'); // Redirect if not logged in or no answers session
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = $_SESSION['exam_id'];
$answers = $_SESSION['answers'];

// Fetch the exam details
$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the questions for the exam
$stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the score
$correct_answers = 0;
foreach ($questions as $index => $question) {
    $correct_answer = $question['correct_answer'];
    if (isset($answers[$index]) && $answers[$index] == $correct_answer) {
        $correct_answers++;
    }
}

$total_questions = count($questions);
$score_percentage = ($correct_answers / $total_questions) * 100;
$status = ($score_percentage >= 50) ? 'Passed' : 'Failed'; // Assuming 50% is the passing threshold

// Store the result in the database
$stmt = $pdo->prepare("INSERT INTO exam_results (user_id, exam_id, correct_answers, total_questions, percentage, status) 
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $exam_id, $correct_answers, $total_questions, $score_percentage, $status]);

// Clear the session data for the current exam
unset($_SESSION['exam_id']);
unset($_SESSION['questions']);
unset($_SESSION['current_question']);
unset($_SESSION['answers']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #F5F5F5;
            color: #212121;
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: #2E004F;
            padding: 10px 20px;
            text-align: center;
            color: #FFEB3B;
            font-size: 20px;
        }

        .navbar a {
            color: #FFEB3B;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .result-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        h2 {
            font-size: 30px;
            color: #2E004F;
            margin-bottom: 20px;
        }

        .result-summary {
            margin-bottom: 30px;
        }

        .result-summary h3 {
            color: #FFEB3B;
            font-size: 24px;
        }

        .result-summary p {
            font-size: 18px;
            color: #212121;
        }

        .result-details {
            margin-top: 30px;
        }

        .result-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .result-details th,
        .result-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .result-details th {
            background-color: #2E004F;
            color: #FFEB3B;
        }

        .result-details tr:hover {
            background-color: #FFEB3B;
        }

        .footer {
            background-color: #212121;
            color: #FFEB3B;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }

        /* Footer fixed at the bottom */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="exam_list.php">Exams</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="result-container">
        <h2>Exam Results</h2>

        <div class="result-summary">
            <h3>Exam: <?= htmlspecialchars($exam['exam_name']) ?></h3>
            <p>Your Score: <?= $correct_answers ?> / <?= $total_questions ?> (<?= number_format($score_percentage, 2) ?>%)</p>
            <p>Status: <?= $status ?></p>
        </div>

        <div class="result-details">
            <h3>Detailed Results</h3>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Your Answer</th>
                        <th>Correct Answer</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $index => $question): ?>
                        <tr>
                            <td><?= htmlspecialchars($question['question_text']) ?></td>
                            <td><?= isset($answers[$index]) ? htmlspecialchars($answers[$index]) : 'No answer' ?></td>
                            <td><?= htmlspecialchars($question['correct_answer']) ?></td>
                            <td>
                                <?= isset($answers[$index]) && $answers[$index] == $question['correct_answer'] ? 'Correct' : 'Incorrect' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="footer">
        <p>&copy; 2024 Exam Management System. All Rights Reserved.</p>
    </div>

</body>

</html>
