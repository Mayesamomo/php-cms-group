<?php
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
require_once "includes/db_connection.php";

// Initialize variables
$user_id = null;
$is_admin = false;

// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    // Get the user ID from the session
    $user_id = $_SESSION["user_id"];

    // Fetch the user's role from the user_profiles table
    $stmt = $pdo->prepare("SELECT role FROM user_profiles WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_profile = $stmt->fetch();

    // Check if the user exists and has a role
    if ($user_profile && isset($user_profile['role'])) {
        // Check if the user's role is 'admin'
        $is_admin = ($user_profile['role'] === 'admin');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Car Depo site</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" type="text/css" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">-->
    <!-- Font Awesome CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <div class="container">
        <a class="navbar-brand text-white" href="index.php">Car Depot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Add your navigation links here -->
            </ul>
        </div>
    </div>
</nav>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Car Depo site</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" type="text/css" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">-->
    <!-- Font Awesome CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <div class="container">
        <a class="navbar-brand text-white" href="index.php">Car Depot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($user_id && $is_admin) : ?>
                    <!-- Show admin-only links -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin_users.php">User Management</a>
                    </li>
                <?php endif; ?>

                <?php if ($user_id) : ?>
                    <!-- Show regular user links -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="view_profile.php">View Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger" href="logout.php">Logout</a>
                    </li>
                <?php else : ?>
                    <!-- Show login and register buttons for non-logged-in users -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-secondary text-white" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

