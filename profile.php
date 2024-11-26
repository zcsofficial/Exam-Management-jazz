<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch institution details
$stmt = $pdo->prepare("SELECT * FROM institutions WHERE institution_id = :institution_id");
$stmt->bindParam(':institution_id', $user['institution_id'], PDO::PARAM_STR);
$stmt->execute();
$institution = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <!-- External Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJp3QhqLMpG8r+Knujsl5/6pwiqJZlh2X4y/X0iXGJ9lzJwV1vI5c6+hfOGZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #F5F5F5;
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background-color: #2E004F;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #FFEB3B !important;
        }

        .navbar-nav .nav-link:hover {
            color: #00BCD4 !important;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #2E004F;
            color: #FFEB3B;
        }

        .card-body {
            background-color: #ffffff;
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #00BCD4;
            border-color: #00BCD4;
        }

        .btn-primary:hover {
            background-color: #FFEB3B;
            border-color: #FFEB3B;
            box-shadow: 0 4px 8px rgba(255, 235, 59, 0.5);
            transition: background-color 0.3s, border-color 0.3s, box-shadow 0.3s;
        }

        .footer {
            background-color: #2E004F;
            color: #FFEB3B;
            padding: 10px 0;
        }

        .footer a {
            color: #FFEB3B;
            text-decoration: none;
        }

        .footer a:hover {
            color: #00BCD4;
        }

        /* Hover effect for card */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Media Query for Mobile Responsiveness */
        @media (max-width: 768px) {
            .card-header h4 {
                font-size: 1.5rem;
            }

            .card-body table th,
            .card-body table td {
                font-size: 0.9rem;
            }

            .navbar-nav .nav-link {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <a class="navbar-brand" href="#">Exam System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="exam_dashboard.php"><i class="fas fa-clipboard-list"></i> Exam Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Profile Information</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <!-- Profile Picture Handling -->
                        <img src="<?= !empty($user['profile_picture']) ? $user['profile_picture'] : 'https://via.placeholder.com/150' ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3">
                        <h5><?= htmlspecialchars($user['username']) ?></h5>
                    </div>
                    <div class="col-md-8">
                        <table class="table">
                            <tr>
                                <th>Email</th>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td><?= htmlspecialchars($user['contact_number']) ?></td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td><?= htmlspecialchars(ucwords($user['role'])) ?></td>
                            </tr>
                            <tr>
                                <th>Institution</th>
                                <td><?= htmlspecialchars($institution['institution_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td><?= htmlspecialchars($institution['location']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="edit_profile.php" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer text-center">
        <p>&copy; <?= date("Y") ?> Exam Management System. All Rights Reserved.</p>
        <p><a href="terms.php">Terms and Conditions</a> | <a href="privacy.php">Privacy Policy</a></p>
    </div>

    <!-- Bootstrap and FontAwesome JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>
