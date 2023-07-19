<?php
// Start the session
session_start();

// Check if the user is logged in as an admin, otherwise redirect to login page
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cars.php">Manage Cars</a>
                </li>
                <!-- Add more navigation links for other management pages -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
