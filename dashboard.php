<?php
session_start();
// Include the database connection
require_once "includes/db_connection.php";

// Function to get user information from the database
function getUserInfo($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch();
}

// Function to get user profile data from the database
function getUserProfile($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch();
}

// Function to get the user's favorite cars from the database
function getFavoriteCars($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT cars.* FROM favorites 
                        INNER JOIN cars ON favorites.car_id = cars.id 
                        WHERE favorites.user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll();
}

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Fetch user information from the database
$user = getUserInfo($pdo, $user_id);

// Check if the user exists
if (!$user) {
    // User not found, redirect to login
    header("Location: login.php");
    exit();
}

// Retrieve user details
$email = $user['email'];

// Fetch additional user profile data (if available)
$user_profile = getUserProfile($pdo, $user_id);

// Check if the user profile exists
$active = ($user_profile && isset($user_profile['active'])) ? $user_profile['active'] : null;
$firstname = ($user_profile && isset($user_profile['first_name'])) ? $user_profile['first_name'] : null;

// Fetch the user's favorite cars
$favorite_cars = getFavoriteCars($pdo, $user_id);

// Get the logged time
$login_time = $_SESSION["login_time"];
$current_time = time();
$time_difference = $current_time - $login_time;
$login_duration = gmdate("H:i:s", $time_difference);
?>

<!-- Include the header -->
<?php require_once "includes/header.php"; ?>

<!-- User Dashboard -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar with user information -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Welcome, <?php echo htmlspecialchars($firstname); ?>!</h4>
                    <?php if ($active === 'true') : ?>
                        <p>Your account is active.</p>
                    <?php else : ?>
                        <p>Your account may have been deactivated.<br/>Please contact the admin.</p>
                    <?php endif; ?>
                    <p>Login duration: <?php echo $login_duration; ?></p>
                    <!-- Add more user details here -->
                </div>
            </div>

            <!-- List of favorite cars -->
            <div class="mt-4">
                <h5>Your Favorite Cars</h5>
                <ul>
                    <?php foreach ($favorite_cars as $car) : ?>
                        <li><?php echo htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <!-- Main content area -->
            <div class="card p-4">
                <h2 class="text-center">Recently Added and Updated</h2>
                <!-- Display recently added cars here -->
                <div class="row mt-4">
                    <?php
                    // Fetch the four most recently added cars from the database
                    $stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC LIMIT 4");
                    $recently_added_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Display the recently added cars
                    foreach ($recently_added_cars as $car) {
                        echo '
                        <div class="col-md-3 mb-3">
                            <div class="card car-card">
                                <img src="public/images/' . htmlspecialchars($car['images']) . '" class="card-img-top" alt="' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . '</h5>
                                    <p class="card-text">Year: ' . htmlspecialchars($car['year']) . '</p>
                                    <p class="card-text">Price: $' . htmlspecialchars($car['price']) . '</p>
                                    <p class="card-text">Quantity: ' . htmlspecialchars($car['quantity']) . '</p>
                                </div>
                            </div>
                            <a href="car_details.php?id=' . htmlspecialchars($car['id']) . '" class="card-link">car details</a>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include the footer -->
<?php require_once "includes/footer.php"; ?>
