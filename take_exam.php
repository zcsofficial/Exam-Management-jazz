<?php
session_start();
require_once 'db.php';

// Check if the student is logged in and has an exam session active
if (!isset($_SESSION['user_id']) || !isset($_SESSION['exam_id']) || !isset($_SESSION['questions'])) {
    header('Location: login.php'); // Redirect if not logged in or no exam session
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = $_SESSION['exam_id'];
$questions = $_SESSION['questions'];
$current_question = $_SESSION['current_question'];

// Handle the answer submission and move to the next question
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['answer'])) {
        $answer = htmlspecialchars($_POST['answer']); // Sanitize input to avoid XSS

        // Store the answer (in real-world apps, you'd save this to a database for grading)
        $_SESSION['answers'][$current_question] = $answer;

        // Move to the next question
        $_SESSION['current_question']++;

        // If last question, redirect to results
        if ($_SESSION['current_question'] >= count($questions)) {
            header('Location: exam_results.php');
            exit();
        }
    } else {
        echo "<script>alert('Please select an answer before proceeding.');</script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJp3QhqLMpG8r+Knujsl5/6pwiqJZlh2X4y/X0iXGJ9lzJwV1vI5c6+hfOGZ" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3>Question <?= $current_question + 1 ?> of <?= count($questions) ?></h3>
            </div>
            <div class="card-body">
                <h4><?= htmlspecialchars($current_question_data['question_text']) ?></h4>

                <form method="POST">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" id="option_a" value="A" required>
                            <label class="form-check-label" for="option_a">
                                <?= htmlspecialchars($current_question_data['option_a']) ?>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" id="option_b" value="B" required>
                            <label class="form-check-label" for="option_b">
                                <?= htmlspecialchars($current_question_data['option_b']) ?>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" id="option_c" value="C" required>
                            <label class="form-check-label" for="option_c">
                                <?= htmlspecialchars($current_question_data['option_c']) ?>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" id="option_d" value="D" required>
                            <label class="form-check-label" for="option_d">
                                <?= htmlspecialchars($current_question_data['option_d']) ?>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Next Question</button>
                </form>
            </div>
            <div class="card-footer text-muted">
                <small>Progress: <?= $current_question + 1 ?> of <?= count($questions) ?></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>
