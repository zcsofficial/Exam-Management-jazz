<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $institution_id = $_POST['institution_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $contact_number = $_POST['contact_number'];

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Check if institution exists
            $sql = "SELECT * FROM institutions WHERE institution_id = :institution_id LIMIT 1"; 
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':institution_id', $institution_id);
            $stmt->execute();
            $institution = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$institution) {
                echo "Error: Institution not found.";
                exit;
            }

            // Insert student data
            $sql = "INSERT INTO users (username, email, password, role, contact_number, institution_id) 
                    VALUES (:username, :email, :password, 'student', :contact_number, :institution_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':institution_id', $institution_id);

            // Execute the query
            if ($stmt->execute()) {
                echo "Registration successful! Please <a href='login.php'>login</a>.";
            } else {
                echo "Error: Something went wrong, please try again later.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register - Exam Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Student Register</h2>
            <form action="student_register.php" method="POST" id="student-register-form">
                <!-- Student Registration Fields -->
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="text" name="institution_id" placeholder="Institution ID" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm-password" placeholder="Confirm Password" required>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_number" placeholder="Contact Number" required>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
