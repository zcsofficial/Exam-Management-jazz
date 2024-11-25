<?php
session_start();
require_once 'db.php';

// Check if the student is logged in and has an exam session active
if (!isset($_SESSION['user_id']) || !isset($_SESSION['exam_id'])) {
    header('Location: login.php'); // Redirect if not logged in or no exam session
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = $_SESSION['exam_id'];
$questions = $_SESSION['questions'];
$current_question = $_SESSION['current_question'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answer = $_POST['answer'];

    // Store the answer (in real-world apps, you'd save this to a database for grading)
    $_SESSION['answers'][$current_question] = $answer;

    // Move to the next question
    $_SESSION['current_question']++;

    // If last question, redirect to results
    if ($_SESSION['current_question'] >= count($questions)) {
        header('Location: exam_results.php');
        exit();
    }
}

$current_question_data = $questions[$current_question];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="exam-container">
        <h2>Question <?= $current_question + 1 ?>: <?= $current_question_data['question_text'] ?></h2>
        
        <form method="POST">
            <label>
                <input type="radio" name="answer" value="A" required> <?= $current_question_data['option_a'] ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="B" required> <?= $current_question_data['option_b'] ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="C" required> <?= $current_question_data['option_c'] ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="D" required> <?= $current_question_data['option_d'] ?>
            </label><br>
            <button type="submit">Next Question</button>
        </form>
    </div>
</body>

</html>
