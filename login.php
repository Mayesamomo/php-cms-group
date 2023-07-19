<?php
// Include the header
require_once "includes/header.php";

// Include the database connection
require_once "includes/db_connection.php";

// Initialize variables
$email = $password = "";
$errors = [];

// Process form submission and user login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user inputs
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Form validation
    if (empty($email)) {
        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors["password"] = "Password is required.";
    }

    // If no errors, proceed with user login
    if (empty($errors)) {
        // Check if the email exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify the password
            if (password_verify($password, $user["password"])) {
                // Check if the user is active
                $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id AND active = 'true'");
                $stmt->execute(['user_id' => $user["id"]]);
                $active_user = $stmt->fetch();

                if ($active_user) {
                    // User login successful
                    // Set user session and redirect to the dashboard
                    session_start();
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["login_time"] = time();
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Account deactivated
                    $errors["login"] = "Your account may have been deactivated.<br/>Please contact the admin.";
                }
            } else {
                // Invalid credentials
                $errors["login"] = "Invalid email or password.";
            }
        } else {
            // Invalid email
            $errors["login"] = "Invalid email or password.";
        }
    }
}
?>

<!-- Login Form -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="text-center">User Login</h2>
                <?php if (isset($errors["login"])) : ?>
                    <div class="alert alert-danger"><?php echo $errors["login"]; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div class="invalid-feedback"><?php echo isset($errors["email"]) ? $errors["email"] : ""; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <div class="invalid-feedback"><?php echo isset($errors["password"]) ? $errors["password"] : ""; ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include the footer -->
<?php require_once "includes/footer.php"; ?>
