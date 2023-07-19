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

// Retrieve user profile data
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_profile = $stmt->fetch();

// Initialize variables for form values and errors
$firstname = ($user_profile && isset($user_profile['first_name'])) ? $user_profile['first_name'] : null;
$lastname = ($user_profile && isset($user_profile['last_name'])) ? $user_profile['last_name'] : null;
$phone = ($user_profile && isset($user_profile['phone'])) ? $user_profile['phone'] : null;
$birthdate = ($user_profile && isset($user_profile['birthdate'])) ? $user_profile['birthdate'] : null;
$gender = ($user_profile && isset($user_profile['gender'])) ? $user_profile['gender'] : null;
$errors = [];

// Process form submission and update user profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user inputs
    $newFirstname = trim($_POST["firstname"]);
    $newLastname = trim($_POST["lastname"]);
    $newPhone = trim($_POST["phone"]);
    $newBirthdate = trim($_POST["birthdate"]);
    $newGender = trim($_POST["gender"]);

    // Form validation
    if (empty($newFirstname)) {
        $errors["firstname"] = "First name is required.";
    }

    if (empty($newLastname)) {
        $errors["lastname"] = "Last name is required.";
    }

    if (empty($newBirthdate)) {
        $errors["birthdate"] = "Birthdate is required.";
    }

    // If no errors, update the user profile
    if (empty($errors)) {
        // Update user profile in the database
        $stmt = $pdo->prepare("UPDATE user_profiles 
                              SET first_name = :firstname, last_name = :lastname, phone = :phone, birthdate = :birthdate, gender = :gender
                              WHERE user_id = :user_id");
        $stmt->execute([
            'firstname' => $newFirstname,
            'lastname' => $newLastname,
            'phone' => $newPhone,
            'birthdate' => $newBirthdate,
            'gender' => $newGender,
            'user_id' => $user_id
        ]);

        // Redirect back to the view_profile.php page after updating
        header("Location: view_profile.php");
        exit();
    }
}
?>

<!-- Include the header -->
<?php require_once "includes/header.php"; ?>

<!-- Edit Profile Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="text-center">Edit Profile</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name:</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo htmlspecialchars($firstname); ?>" required>
                        <div class="invalid-feedback"><?php echo isset($errors["firstname"]) ? $errors["firstname"] : ""; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name:</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo htmlspecialchars($lastname); ?>" required>
                        <div class="invalid-feedback"><?php echo isset($errors["lastname"]) ? $errors["lastname"] : ""; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Birthdate:</label>
                        <input type="date" name="birthdate" id="birthdate" class="form-control" value="<?php echo htmlspecialchars($birthdate); ?>" required>
                        <div class="invalid-feedback"><?php echo isset($errors["birthdate"]) ? $errors["birthdate"] : ""; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender:</label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include the footer -->
<?php require_once "includes/footer.php"; ?>
