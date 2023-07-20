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
$stmt = $pdo->prepare("
    SELECT users.*, user_profiles.first_name, user_profiles.last_name, user_profiles.phone, user_profiles.role, user_profiles.active, user_profiles.last_login
    FROM users
    LEFT JOIN user_profiles ON users.id = user_profiles.user_id
    WHERE users.id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

// Check if the user exists in the database
if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// Check if the form is submitted for updating the user
if (isset($_POST['update_user'])) {
    // Get user information from the form submission
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $active = $_POST['active'];

    // Update the user information in the database
    $stmt = $pdo->prepare("
        UPDATE user_profiles
        SET first_name = :first_name, last_name = :last_name
        WHERE id = :user_id
    ");
    $stmt->execute(['first_name' => $first_name, 'last_name' => $last_name, 'user_id' => $user_id]);

    // Update the user profile information in the database
    $stmt = $pdo->prepare("
        UPDATE user_profiles
        SET phone = :phone, role = :role, active = :active
        WHERE user_id = :user_id
    ");
    $stmt->execute(['phone' => $phone, 'role' => $role, 'active' => $active, 'user_id' => $user_id]);

    // Set a session variable with the success message
    $_SESSION['success_message'] = "User information successfully updated.";

    // Redirect back to the edit_user.php page with a success message
    header("Location: edit_user.php?id=$user_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Edit User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="post">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role">
                <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="normal" <?= ($user['role'] === 'normal') ? 'selected' : '' ?>>Normal</option>
            </select>
        </div>
        <div class="form-group">
            <label for="active">Active</label>
            <select class="form-control" id="active" name="active">
                <option value="true" <?= ($user['active'] === 'true') ? 'selected' : '' ?>>True</option>
                <option value="false" <?= ($user['active'] === 'false') ? 'selected' : '' ?>>False</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" name="update_user">Update User</button>
    </form>
    <!-- Check for the success message and display it -->
    <?php if (isset($_SESSION['success_message'])) : ?>
        <div class="alert alert-success mt-3">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
</div>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>
