<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

if ($_SESSION['role'] != 'student') {
    // If user is not a student, redirect to admin dashboard
    header('Location: admin_dashboard.php');
    exit();
}

// Get the logged-in user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT u.username, u.role, u.institution_id, i.institution_name, i.location, i.contact, i.email, i.website
                       FROM users u
                       JOIN institutions i ON u.institution_id = i.institution_id
                       WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user not found, redirect to login
if (!$user) {
    header('Location: login.php');
    exit();
}

// Extract user and institution data
$username = htmlspecialchars($user['username']);
$role = htmlspecialchars($user['role']);
$institution_name = htmlspecialchars($user['institution_name']);
$location = htmlspecialchars($user['location']);
$contact = htmlspecialchars($user['contact']);
$email = htmlspecialchars($user['email']);
$website = htmlspecialchars($user['website']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #F5F5F5; /* Soft Off-White background */
            color: #212121; /* Deep Charcoal text color */
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #2E004F; /* Dark Purple */
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar .logo {
            font-size: 24px;
            color: #FFEB3B; /* Electric Yellow */
            font-weight: 500;
        }

        .navbar .nav-links a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-size: 16px;
        }

        .navbar .nav-links a:hover {
            color: #00BCD4; /* Neon Blue */
        }

        .navbar .nav-links a.active {
            color: #00BCD4; /* Neon Blue for active link */
        }

        /* Dashboard Container */
        .dashboard-container {
            padding: 30px;
            max-width: 1200px;
            margin: 50px auto;
            flex-grow: 1; /* Ensures content takes up remaining space */
        }

        h2 {
            font-size: 30px;
            color: #2E004F;
            margin-bottom: 20px;
        }

        .institution-details h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #2E004F;
        }

        .institution-details p {
            font-size: 16px;
            margin: 8px 0;
        }

        /* Card Styles */
        .card-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background-color: #212121; /* Deep Charcoal */
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            background-color: #FFEB3B; /* Electric Yellow */
        }

        .card i {
            font-size: 40px;
            color: #00BCD4; /* Neon Blue */
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #fff;
        }

        .card p {
            font-size: 16px;
            color: #ddd;
        }

        /* Footer Styles */
        .footer {
            background-color: #212121; /* Deep Charcoal */
            color: #FFEB3B; /* Electric Yellow */
            text-align: center;
            padding: 10px 0;
            margin-top: 40px;
            font-size: 14px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-container {
                grid-template-columns: 1fr 1fr;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .nav-links {
                margin-top: 10px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .nav-links a {
                margin-left: 0;
                margin-bottom: 10px;
            }
        }

    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">Exam Management System</div>
        <div class="nav-links">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="results.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">Results</a>
            <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Dashboard container -->
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $username; ?>!</h2>

        <!-- Institution details -->
        <div class="institution-details">
            <h3>Your Registered Institution</h3>
            <p><strong>Name:</strong> <?php echo $institution_name; ?></p>
            <p><strong>Location:</strong> <?php echo $location; ?></p>
            <p><strong>Contact:</strong> <?php echo $contact; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Website:</strong> <a href="https://<?php echo $website; ?>" target="_blank"><?php echo $website; ?></a></p>
        </div>

        <!-- Dashboard cards -->
        <div class="card-container">
            <div class="card">
                <a href="exam.php">
                    <i class="fas fa-pencil-alt"></i>
                    <h3>Manage Exams</h3>
                    <p>View and manage your upcoming exams.</p>
                </a>
            </div>
            <div class="card">
                <a href="student_dashboard.php">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Student Dashboard</h3>
                    <p>View your exam history and results.</p>
                </a>
            </div>
            <div class="card">
                <a href="exam_results.php">
                    <i class="fas fa-trophy"></i>
                    <h3>Results</h3>
                    <p>Check your exam results and scores.</p>
                </a>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Exam Management System | All Rights Reserved</p>
    </div>

</body>

</html>
