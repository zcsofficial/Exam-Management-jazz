<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        /* Custom Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background-color: #F5F5F5;
    color: #212121;
}

.welcome-message h1 span {
    color: #2E004F;
}

.auth-buttons .btn {
    padding: 15px 30px;
    font-size: 1.2rem;
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
}

.btn-primary {
    background-color: #FFEB3B;
    border: none;
    color: #2E004F;
}

.btn-secondary {
    background-color: #2E004F;
    border: none;
    color: #FFEB3B;
}

.btn:hover {
    transform: scale(1.05);
    opacity: 0.9;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .welcome-message h1 {
        font-size: 2rem;
    }

    .auth-buttons .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

    </style>
</head>

<body>
    <div class="container text-center mt-5">
        <div class="welcome-message" data-aos="fade-up">
            <h1 class="display-4 fw-bold">Welcome to <span class="text-warning">Exam Management System</span></h1>
            <p class="lead mt-3">Access your exams and results</p>
        </div>

        <div class="auth-buttons mt-4">
            <a href="login.php" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="register.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init(); // Initialize AOS
    </script>
</body>

</html>
