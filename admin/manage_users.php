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

// Check if the user clicked the "Delete" button for a specific user
if (isset($_POST['delete_user'])) {
    // Get the user ID from the form submission
    $user_id = intval($_POST['user_id']);

    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    // Set a session variable with the success message
    $_SESSION['success_message'] = "User successfully deleted.";
}

// Function to fetch all users and their profiles from the database
function getAllUsersWithProfiles($pdo) {
    $stmt = $pdo->prepare("
        SELECT users.id, users.email, user_profiles.first_name, user_profiles.last_name, user_profiles.last_login
        FROM users
        LEFT JOIN user_profiles ON users.id = user_profiles.user_id
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all users with their profiles from the database
$users = getAllUsersWithProfiles($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Admin - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles.css" type="text/css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark">
        <div class="container">
            <a class="navbar-brand text-white" href="../dashboard.php">Admin Dashboard</a>
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

    <div class="container mt-5 mb-5">
        <!-- Check for the success message and display it -->
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <h2>Manage Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Last Login</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['first_name']; ?></td>
                        <td><?php echo $user['last_name']; ?></td>
                        <td><?php echo ($user['last_login']) ? date('Y-m-d H:i:s', strtotime($user['last_login'])) : 'N/A'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_user.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Include the footer -->
    <?php require_once "../includes/footer.php"; ?>

