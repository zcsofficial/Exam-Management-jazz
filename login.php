<?php
session_start();
require_once 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data with basic sanitization
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($username && $password) {
        try {
            // Check if the user exists
            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id();

                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['institution_id'] = $user['institution_id'] ?? null; // Optional

                    // Redirect based on user role
                    if ($user['role'] === 'student') {
                        header("Location: dashboard.php"); // Redirect to student dashboard
                    } elseif ($user['role'] === 'admin') {
                        header("Location: admin.php"); // Redirect to admin dashboard
                    }
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "User not found.";
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again later.";
            // Log error for debugging (don't expose to user)
            error_log($e->getMessage());
        }
    } else {
        $error = "All fields are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Exam Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container d-flex align-items-center justify-content-center vh-100">
        <div class="auth-form bg-dark text-white p-4 rounded shadow">
            <h2 class="text-center mb-4">Login</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Login</button>
                <p class="mt-3 text-center">
                    Don't have an account? <a href="register.php" class="text-warning">Register</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

