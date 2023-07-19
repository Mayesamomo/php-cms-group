<?php
// Start the session
session_start();

// Include the header
require_once "includes/header.php";

// Include the database connection
require_once "includes/db_connection.php";

// Function to check if a car is already in user's favorites
function isCarInFavorites($pdo, $user_id, $car_id)
{
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND car_id = :car_id");
    $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id]);
    return $stmt->fetch() !== false;
}

// Check if the user is logged in
$showAddToFavoritesButton = false;
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $showAddToFavoritesButton = true;

    // Check if the user has already added this car to favorites
    if (isset($_GET["id"])) {
        $car_id = intval($_GET["id"]);
        $isFavorite = isCarInFavorites($pdo, $user_id, $car_id);
    }
}

// Get the car ID from the query parameter
if (isset($_GET["id"])) {
    $car_id = intval($_GET["id"]);

    // Fetch car details from the database
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = :car_id");
    $stmt->execute(['car_id' => $car_id]);
    $car = $stmt->fetch();
}

// Check if car with given ID exists in the database
if (!$car) {
    // Car not found, redirect to car listing page
    header("Location: index.php");
    exit();
}

// Check if the car has an image, if not, use the placeholder image URL
$image_url = (!empty($car['images'])) ? $car['image_url'] : "https://placehold.co/400x400";
?>
<!-- ... Previous code ... -->

<!-- Car Details Page -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <img src="<?= $image_url ?>" class="card-img-top" alt="<?= $car['make'] . ' ' . $car['model'] ?>">
                <div class="card-body">
                    <h2 class="card-title"><?= $car['make'] . ' ' . $car['model'] ?></h2>
                    <p class="card-text">Year: <?= $car['year'] ?></p>
                    <p class="card-text">Price: $<?= $car['price'] ?></p>
                    <!-- Add more car details here -->

                    <?php if ($showAddToFavoritesButton) : ?>
                        <?php if (!$isFavorite) : ?>
                            <!-- Show "Add to Favorites" button if the car is not in favorites -->
                            <form action="add_to_favorites.php" method="post">
                                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                                <button type="submit" class="btn btn-primary">Add to Favorites</button>
                            </form>
                        <?php else : ?>
                            <!-- Show "Remove from Favorites" button if the car is in favorites -->
                            <form action="add_to_favorites.php" method="post">
                                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                                <button type="submit" class="btn btn-danger">Remove from Favorites</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION["add_to_favorites_message"])) : ?>
                        <p class="text-success"><?= $_SESSION["add_to_favorites_message"] ?></p>
                        <?php unset($_SESSION["add_to_favorites_message"]); ?>
                    <?php elseif (isset($_SESSION["remove_from_favorites_message"])) : ?>
                        <p class="text-danger"><?= $_SESSION["remove_from_favorites_message"] ?></p>
                        <?php unset($_SESSION["remove_from_favorites_message"]); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Include the footer -->
<?php require_once "includes/footer.php"; ?>
