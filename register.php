
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Exam Management System</title>
    <!-- External Libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #121212;
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
            background-color: #222;
            padding: 20px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .auth-form h2 {
            margin-bottom: 20px;
            color: #f44336;
        }
        .btn-primary {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .btn-primary:hover {
            background-color: #d32f2f;
        }
        p {
            margin-top: 20px;
            text-align: center;
        }
        .role-selection i {
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Register</h2>
            
                <!-- Buttons for Institution and Student Registration -->
                <a href="institution_register.php">
                <button type="button" class="btn-primary">
                    <i class="fas fa-university"></i> Institution Registration
                </button>
            </a>
            <br>
            <a href="student_register.php">
                <button type="button" class="btn-primary">
                    <i class="fas fa-user-graduate"></i> Student Registration
                </button>
            </a>
           
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
