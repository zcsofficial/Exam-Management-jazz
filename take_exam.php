<?php
session_start();
require_once 'db.php'; // Include the updated database connection

// Check if the student is logged in and has an exam session active
if (!isset($_SESSION['user_id']) || !isset($_SESSION['exam_id']) || !isset($_SESSION['questions'])) {
    header('Location: login.php'); // Redirect if not logged in or no exam session
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = $_SESSION['exam_id'];
$questions = $_SESSION['questions'];
$current_question = isset($_SESSION['current_question']) ? $_SESSION['current_question'] : 0; // Initialize if not set

// Check the attendance status from the exam_attendance table using PDO
$stmt = $pdo->prepare("SELECT attended_at FROM exam_attendance WHERE user_id = :user_id AND exam_id = :exam_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
$stmt->execute();
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);
$attended_at = $attendance['attended_at'];

// Handle the answer submission and move to the next question
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['answer'])) {
        $answer = htmlspecialchars($_POST['answer']); // Sanitize input to avoid XSS

        // Store the answer (in real-world apps, you'd save this to a database for grading)
        $_SESSION['answers'][$current_question] = $answer;

        // Move to the next question
        $_SESSION['current_question']++;

        // If last question, mark the exam as completed and store the result
        if ($_SESSION['current_question'] >= count($questions)) {
            // Calculate correct answers and percentage
            $correct_answers = 0;
            foreach ($questions as $index => $question) {
                if (isset($_SESSION['answers'][$index]) && $_SESSION['answers'][$index] == $question['correct_answer']) {
                    $correct_answers++;
                }
            }
            $total_questions = count($questions);
            $percentage = ($correct_answers / $total_questions) * 100;

            // Determine the status (Pass or Fail)
            $status = $percentage >= 50 ? 'Passed' : 'Failed';

            // Save the result to the database
            $stmt = $pdo->prepare("INSERT INTO exam_results (user_id, exam_id, correct_answers, total_questions, percentage, status) VALUES (:user_id, :exam_id, :correct_answers, :total_questions, :percentage, :status)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
            $stmt->bindParam(':correct_answers', $correct_answers, PDO::PARAM_INT);
            $stmt->bindParam(':total_questions', $total_questions, PDO::PARAM_INT);
            $stmt->bindParam(':percentage', $percentage, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();

            // Mark exam attendance if not already marked
            if (is_null($attended_at)) {
                $stmt = $pdo->prepare("INSERT INTO exam_attendance (user_id, exam_id) VALUES (:user_id, :exam_id)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Redirect to results page
            header('Location: exam_results.php');
            exit();
        }
    } else {
        echo "<script>alert('Please select an answer before proceeding.');</script>";
    }
}

// Get the current question data
$current_question_data = $questions[$current_question];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJp3QhqLMpG8r+Knujsl5/6pwiqJZlh2X4y/X0iXGJ9lzJwV1vI5c6+hfOGZ" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Exam System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

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

                    <!-- Conditional button based on exam status -->
                    <?php if ($current_question >= count($questions)) { ?>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Complete Exam</button>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-arrow-right"></i> Next Question</button>
                    <?php } ?>
                </form>
            </div>
            <div class="card-footer text-muted">
                <small>Progress: <?= $current_question + 1 ?> of <?= count($questions) ?></small>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; <?= date("Y") ?> Exam System. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>
