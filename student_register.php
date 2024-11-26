<?php
require_once 'db.php';

$errorMessage = '';  // Initialize an empty error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $institution_id = $_POST['institution_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $contact_number = $_POST['contact_number'];

    // Check if the passwords match
    if ($password !== $confirm_password) {
        $errorMessage = 'Passwords do not match. Please try again.';
    } else {
        // Check if the username already exists
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $errorMessage = 'Username already exists. Please choose a different username.';
        } else {
            // Proceed with registration if no errors
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                // Check if the institution exists
                $sql = "SELECT * FROM institutions WHERE institution_id = :institution_id LIMIT 1"; 
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':institution_id', $institution_id);
                $stmt->execute();
                $institution = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$institution) {
                    $errorMessage = 'Institution not found. Please provide a valid Institution ID.';
                } else {
                    // Insert the new user
                    $sql = "INSERT INTO users (username, email, password, role, contact_number, institution_id) 
                            VALUES (:username, :email, :password, 'student', :contact_number, :institution_id)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':contact_number', $contact_number);
                    $stmt->bindParam(':institution_id', $institution_id);

                    if ($stmt->execute()) {
                        echo "<script>alert('Registration successful! Please login.');</script>";
                        header('Location: login.php');
                        exit();
                    } else {
                        $errorMessage = 'Something went wrong, please try again later.';
                    }
                }
            } catch (PDOException $e) {
                $errorMessage = 'Error: ' . $e->getMessage();
            }
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
    <!-- External Libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2E004F;
            color: white;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .auth-form {
            background-color: #212121;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .auth-form h2 {
            margin-bottom: 20px;
            color: #FFEB3B;
            font-size: 1.8rem;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            padding-left: 40px;
            border: 2px solid #FFEB3B;
            border-radius: 5px;
            font-size: 1.1rem;
            background-color: #fff;
            color: #212121;
        }

        .input-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #FFEB3B;
        }

        button[type="submit"] {
            background-color: #FFEB3B;
            color: #2E004F;
            border: none;
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2E004F;
            color: #FFEB3B;
        }

        p {
            margin-top: 20px;
        }

        a {
            color: #FFEB3B;
            text-decoration: none;
        }

        a:hover {
            color: #FFEB3B;
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 1.1rem;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ffeb3b;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Student Registration</h2>
            <?php if ($errorMessage): ?>
                <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <form action="student_register.php" method="POST" id="student-register-form">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-id-badge"></i>
                    <input type="text" name="institution_id" placeholder="Institution ID" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm-password" placeholder="Confirm Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="contact_number" placeholder="Contact Number" required>
                </div>

                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
