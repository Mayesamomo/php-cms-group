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

// Check if the car ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to manage_cars.php if the car ID is missing or invalid
    header("Location: manage_cars.php");
    exit();
}

$car_id = intval($_GET['id']);

// Fetch car data from the database
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = :car_id");
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the car with the provided ID exists
if (!$car) {
    // Redirect to manage_cars.php if the car does not exist
    header("Location: manage_cars.php");
    exit();
}

// Initialize variables to store form data and error messages
$make = $car['make'];
$model = $car['model'];
$year = $car['year'];
$price = $car['price'];
$quantity = $car['quantity'];
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get updated car details from the form
    $make = trim($_POST["make"]);
    $model = trim($_POST["model"]);
    $year = trim($_POST["year"]);
    $price = trim($_POST["price"]);
    $quantity = trim($_POST["quantity"]);
    $images = $_FILES["car_image"];

    // Validate form data
    if (empty($make) || empty($model) || empty($year) || empty($price) || empty($quantity)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Check if a new image is uploaded
        if ($images['size'] > 0) {
            // Validate image upload
            $upload_dir = "../public/images/"; // Path to the images folder inside the public folder
            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            $file_name = basename($images["name"]);
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                $error_message = "Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($images["size"] > 2097152) { // Limit image size to 2MB
                $error_message = "Image size exceeds the maximum allowed limit (2MB).";
            } else {
                // Move the uploaded image to the images folder
                if (move_uploaded_file($images["tmp_name"], $upload_dir . $file_name)) {
                    // Delete the old image if it exists
                    if ($car['images']) {
                        unlink($upload_dir . $car['images']);
                    }

                    // Update the car data in the database with the new image path
                    $stmt = $pdo->prepare("UPDATE cars SET make = :make, model = :model, year = :year, price = :price, quantity = :quantity, images = :images WHERE id = :car_id");
                    $result = $stmt->execute([
                        'make' => $make,
                        'model' => $model,
                        'year' => $year,
                        'price' => $price,
                        'quantity' => $quantity,
                        'images' => $file_name,
                        'car_id' => $car_id,
                    ]);

                    // Check if the database update was successful
                    if ($result) {
                        $_SESSION['success_message'] = "Car details updated successfully.";
                        // Redirect back to edit_car.php to avoid resubmission on refresh
                        header("Location: edit_car.php?id={$car_id}");
                        exit();
                    } else {
                        $error_message = "Error occurred while updating car details.";
                    }
                } else {
                    $error_message = "Error uploading image. Please try again.";
                }
            }
        } else {
            // Update the car data in the database without changing the image
            $stmt = $pdo->prepare("UPDATE cars SET make = :make, model = :model, year = :year, price = :price, quantity = :quantity WHERE id = :car_id");
            $result = $stmt->execute([
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'price' => $price,
                'quantity' => $quantity,
                'car_id' => $car_id,
            ]);

            // Check if the database update was successful
            if ($result) {
                $_SESSION['success_message'] = "Car details updated successfully.";
                // Redirect back to edit_car.php to avoid resubmission on refresh
                header("Location: edit_car.php?id={$car_id}");
                exit();
            } else {
                $error_message = "Error occurred while updating car details.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Edit Car - Car Depot</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark">
        <div class="container">
            <a class="navbar-brand text-white" href="../dashboard.php">Admin Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="./manage_cars.php">Back </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Check for success message and display it -->
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Check for error message and display it -->
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>


        <div class="container mt-5">
        <h2>Edit Car</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="make" class="form-label">Make</label>
                <input type="text" class="form-control" id="make" name="make" value="<?= htmlspecialchars($make) ?>" required>
            </div>
            <div class="mb-3">
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($model) ?>" required>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($year) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price) ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>" required>
            </div>
            <div class="mb-3">
                <label for="car_image" class="form-label">Images</label>
                <input type="file" class="form-control" id="car_image" name="car_image" accept="image/*">
            </div>
            <?php if ($error_message) : ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>
            <button type="submit" name="update_car" class="btn btn-primary mb-2">Update Car</button>
        </form>
        </div>
    </div>
    <!-- Include the footer -->
    <?php require_once "../includes/footer.php"; ?>
