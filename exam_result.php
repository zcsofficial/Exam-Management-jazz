<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all exams attended by the user and their results
$stmt = $pdo->prepare("SELECT exams.exam_name, exam_results.correct_answers, exam_results.total_questions, exam_results.percentage, exam_results.status 
                       FROM exam_results 
                       JOIN exams ON exam_results.exam_id = exams.id
                       WHERE exam_results.user_id = ?");
$stmt->execute([$user_id]);
$exam_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="result-container">
        <h2>Exam Results</h2>
        <p>Student: <?= $_SESSION['username'] ?></p>

        <!-- Show all exams attended by the user -->
        <div class="all-exams">
            <h3>Exams Attended</h3>
            <table>
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Correct Answers</th>
                        <th>Total Questions</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($exam_results) > 0): ?>
                        <?php foreach ($exam_results as $result): ?>
                            <tr>
                                <td><?= htmlspecialchars($result['exam_name']) ?></td>
                                <td><?= $result['correct_answers'] ?></td>
                                <td><?= $result['total_questions'] ?></td>
                                <td><?= round($result['percentage'], 2) ?>%</td>
                                <td><?= $result['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No exams attended yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Show the latest exam result (most recent exam attended) -->
        <?php if (count($exam_results) > 0): ?>
            <div class="latest-result">
                <?php
                // Get the most recent exam result (first result from the array)
                $latest_result = $exam_results[0];
                $exam_name = $latest_result['exam_name'];
                $correct_answers = $latest_result['correct_answers'];
                $total_questions = $latest_result['total_questions'];
                $percentage = $latest_result['percentage'];
                $status = $latest_result['status'];
                ?>

                <h3>Latest Exam Result: <?= $exam_name ?></h3>
                <div class="result-summary">
                    <p>Status: <?= $status ?></p>
                    <p>Correct Answers: <?= $correct_answers ?> / <?= $total_questions ?></p>
                    <p>Percentage: <?= round($percentage, 2) ?>%</p>
                </div>

                <div class="pie-chart-container">
                    <canvas id="resultChart" width="400" height="400"></canvas>
                </div>

                <script>
                    // Prepare data for pie chart
                    const data = {
                        labels: ['Correct', 'Incorrect'],
                        datasets: [{
                            data: [<?= $correct_answers ?>, <?= $total_questions - $correct_answers ?>],
                            backgroundColor: ['#4CAF50', '#F44336'],
                            borderColor: ['#4CAF50', '#F44336'],
                            borderWidth: 1
                        }]
                    };

                    // Create the pie chart
                    const ctx = document.getElementById('resultChart').getContext('2d');
                    const resultChart = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.label + ': ' + tooltipItem.raw + ' (' + Math.round((tooltipItem.raw / <?= $total_questions ?>) * 100) + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>
