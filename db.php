<?php
// Database credentials
$host = 'localhost'; // or the IP address of your database server
$dbname = 'exam_management_system'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

// Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname";

try {
    // Create a PDO instance (connect to the database)
    $pdo = new PDO($dsn, $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Uncomment the line below to test the connection
    // echo "Connected to the database successfully.";

} catch (PDOException $e) {
    // If connection fails, output error message
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
