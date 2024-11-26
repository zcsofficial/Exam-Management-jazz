<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $institution_name = $_POST['institution_name'];
    $institution_id = $_POST['institution_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $contact_number = $_POST['contact_number'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match.');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "SELECT * FROM institutions WHERE institution_id = :institution_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':institution_id', $institution_id);
            $stmt->execute();
            $institution = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$institution) {
                $sql = "INSERT INTO institutions (institution_id, institution_name) 
                        VALUES (:institution_id, :institution_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':institution_id', $institution_id);
                $stmt->bindParam(':institution_name', $institution_name);
                $stmt->execute();
            }

            $sql = "INSERT INTO users (username, email, password, role, contact_number, institution_id, institution_name) 
                    VALUES (:username, :email, :password, 'admin', :contact_number, :institution_id, :institution_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':institution_id', $institution_id);
            $stmt->bindParam(':institution_name', $institution_name);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful! Please login.');</script>";
                header('Location: login.php');
            } else {
                echo "<script>alert('Error: Something went wrong, please try again later.');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institution Register - Exam Management System</title>
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Institution Registration</h2>
            <form action="institution_register.php" method="POST" id="institution-register-form">
                <div class="input-group">
                    <i class="fas fa-university"></i>
                    <input type="text" name="institution_name" placeholder="Institution Name" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-id-badge"></i>
                    <input type="text" name="institution_id" placeholder="Institution ID" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
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
                <button type="submit" class="btn btn-primary">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
