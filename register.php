<?php
// Include the header
require_once "includes/header.php";

// Include the database connection
require_once "includes/db_connection.php";

// Initialize variables
$email = $password = $confirm_password = $first_name = $last_name = $phone = "";
$errors = [];

// Process form submission and user registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user inputs
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $phone = trim($_POST["phone"]);

    // Form validation
    if (empty($email)) {
        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format.";
    } else {
        // Check if the email already exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $errors["email"] = "Email already exists.";
        }
    }

    if (empty($password)) {
        $errors["password"] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors["confirm_password"] = "Passwords do not match.";
    }

    // If no errors, proceed with user registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the users table
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $stmt->execute(['email' => $email, 'password' => $hashed_password]);

        // Get the newly inserted user's ID
        $user_id = $pdo->lastInsertId();

        // Insert additional user profile data into the user_profiles table
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, phone) VALUES (:user_id, :first_name, :last_name, :phone)");
        $stmt->execute(['user_id' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'phone' => $phone]);

        // Redirect to a success page or login page after registration
        header("Location: registration_success.php");
        exit();
    }
}
?>

<!-- Register Form -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="text-center">User Registration</h2>
                <form method="post">
                    <!-- Form fields for user registration -->
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
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <div class="invalid-feedback"><?php echo isset($errors["confirm_password"]) ? $errors["confirm_password"] : ""; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include the footer -->
<?php require_once "includes/footer.php"; ?>
