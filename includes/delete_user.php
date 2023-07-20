<?php
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
require_once "../includes/db_connection.php";

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT role FROM user_profiles WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_profile = $stmt->fetch();

if (!$user_profile || $user_profile['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Check if a user ID is provided in the query parameter
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

// Check if the user exists in the database
if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// Check if the form is submitted for deleting the user
if (isset($_POST['delete_user'])) {
    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    // Set a session variable with the success message
    $_SESSION['success_message'] = "User successfully deleted.";

    // Redirect to the manage_users.php page with a success message
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Delete User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <div class="container">
        <a class="navbar-brand text-white" href="admin_index.php">Admin Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="../dashboard.php">Back to User Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Delete User</h2>
    <p>Are you sure you want to delete this user?</p>
    <form method="post">
        <button type="submit" class="btn btn-danger" name="delete_user">Delete User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php
// Show success message if it exists
if (isset($_SESSION['success_message'])) {
    echo '<div class="container mt-3"><div class="alert alert-success">' . $_SESSION['success_message'] . '</div></div>';
    unset($_SESSION['success_message']);
}
?>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>
