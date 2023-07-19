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

// Function to check if a car is already in user's favorites
function isCarInFavorites($pdo, $user_id, $car_id)
{
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND car_id = :car_id");
    $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id]);
    return $stmt->fetch() !== false;
}

// Get the car ID from the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["car_id"])) {
    $car_id = intval($_POST["car_id"]);
    
    // Get the user ID from the session
    $user_id = $_SESSION["user_id"];

    // Check if the car is already in favorites
    $isFavorite = isCarInFavorites($pdo, $user_id, $car_id);

    if (!$isFavorite) {
        // Add the car to favorites
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, car_id) VALUES (:user_id, :car_id)");
        $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id]);

        // Redirect back to the car details page with a success message
        header("Location: car_details.php?id=" . $car_id . "&added=true");
        exit();
    } else {
        // Remove the car from favorites
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id AND car_id = :car_id");
        $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id]);

        // Redirect back to the car details page with a success message
        header("Location: car_details.php?id=" . $car_id . "&removed=true");
        exit();
    }
} else {
    // If the form is not submitted properly, redirect to the homepage
    header("Location: index.php");
    exit();
}
?>
