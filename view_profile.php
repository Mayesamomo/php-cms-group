<?php
// Start the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
require_once "includes/db_connection.php";

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Fetch the user's information from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

// Check if the user exists
if (!$user) {
    // User not found, redirect to login
    header("Location: login.php");
    exit();
}

// Retrieve user details
$email = $user['email'];

// Fetch additional user profile data (if available)
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_profile = $stmt->fetch();

// Check if the user profile exists
$firstname = ($user_profile && isset($user_profile['first_name'])) ? $user_profile['first_name'] : null;
$lastname = ($user_profile && isset($user_profile['last_name'])) ? $user_profile['last_name'] : null;
$birthdate = ($user_profile && isset($user_profile['birthdate'])) ? $user_profile['birthdate'] : null;
$gender = ($user_profile && isset($user_profile['gender'])) ? $user_profile['gender'] : null;

// Format the birthdate
$formatted_birthdate = ($birthdate) ? date("Y-m-d", strtotime($birthdate)) : "N/A";
?>
    <!-- Include the header -->
    <?php require_once "includes/header.php"; ?>

    <!-- Profile Information -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h2 class="text-center">Your Profile</h2>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstname); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastname); ?></p>
                    <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($formatted_birthdate); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                    <a href="edit_profile.php" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the footer -->
    <?php require_once "includes/footer.php"; ?>

