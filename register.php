<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Exam Management System</title>
    <!-- External Libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* General Styling */
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
            width: 350px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .auth-form h2 {
            margin-bottom: 20px;
            color: #FFEB3B;
        }

        .btn-primary {
            background-color: #FFEB3B;
            color: #2E004F;
            border: none;
            padding: 10px 20px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-primary:hover {
            background-color: #2E004F;
            color: #FFEB3B;
        }

        p {
            margin-top: 20px;
            color: #FFEB3B;
        }

        p a {
            color: #FFEB3B;
            text-decoration: none;
        }

        p a:hover {
            color: white;
        }

        .role-selection i {
            margin-right: 10px;
            color: #2E004F;
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
