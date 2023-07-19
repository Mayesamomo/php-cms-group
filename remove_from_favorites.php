<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
require_once "includes/db_connection.php";

// Check if the car ID is submitted through the form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["car_id"])) {
    // Get the car ID from the form submission
    $car_id = intval($_POST["car_id"]);

    // Remove the car from the user's favorites in the database
    $user_id = $_SESSION["user_id"];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id AND car_id = :car_id");
    $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id]);
}

// Redirect back to the previous page (the car details page) after removing from favorites
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: car_listing.php");
}
exit();
?>
