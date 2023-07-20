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

// Handle form submission when the "Update Car" button is clicked
if (isset($_POST['update_car'])) {
    // Get updated car details from the form
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    
    // Check if a new image is uploaded
    if ($_FILES['car_image']['size'] > 0) {
        // Process image upload
        $image_path = "../public/images/";
        $image_name = $_FILES['car_image']['name'];
        $image_tmp = $_FILES['car_image']['tmp_name'];
        $image_type = $_FILES['car_image']['type'];
        $image_size = $_FILES['car_image']['size'];

        // Check if the uploaded file is an image
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        if (!in_array($image_extension, $allowed_types)) {
            $_SESSION['error_message'] = "Only JPG, JPEG, PNG, and GIF images are allowed.";
            header("Location: edit_car.php?id={$car_id}");
            exit();
        }

        // Generate a unique name for the image
        $new_image_name = uniqid('car_') . '.' . $image_extension;

        // Move the uploaded image to the images folder
        if (!move_uploaded_file($image_tmp, $image_path . $new_image_name)) {
            $_SESSION['error_message'] = "Error occurred while uploading the image.";
            header("Location: edit_car.php?id={$car_id}");
            exit();
        }

        // Delete the old image if it exists
        if ($car['images']) {
            unlink($image_path . $car['images']);
        }
        
        // Update the car data in the database with the new image
        $stmt = $pdo->prepare("UPDATE cars SET make = :make, model = :model, year = :year, price = :price, quantity = :quantity, images = :images WHERE id = :car_id");
        $result = $stmt->execute(['make' => $make, 'model' => $model, 'year' => $year, 'price' => $price, 'quantity' => $quantity, 'images' => $new_image_name, 'car_id' => $car_id]);
    } else {
        // Update the car data in the database without changing the image
        $stmt = $pdo->prepare("UPDATE cars SET make = :make, model = :model, year = :year, price = :price, quantity = :quantity WHERE id = :car_id");
        $result = $stmt->execute(['make' => $make, 'model' => $model, 'year' => $year, 'price' => $price, 'quantity' => $quantity, 'car_id' => $car_id]);
    }

    // Set a session variable with the success/error message
    if ($result) {
        $_SESSION['success_message'] = "Car details updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error occurred while updating car details.";
    }

    // Redirect back to edit_car.php to avoid resubmission on refresh
    header("Location: edit_car.php?id={$car_id}");
    exit();
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

        <h2>Edit Car</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="make">Make:</label>
                <input type="text" class="form-control" id="make" name="make" value="<?= htmlspecialchars($car['make']) ?>" required>
            </div>
            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
            </div>
            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" class="form-control" id="year" name="year" value="<?= $car['year'] ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $car['price'] ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?= $car['quantity'] ?>" required>
            </div>
            <div class="form-group pb-2">
                <label for="car_image">Upload Image:</label>
                <input type="file" class="form-control-file" id="car_image" name="car_image">
            </div>
            <div class="form-group pb-4">
                <img src="../public/images/<?= htmlspecialchars($car['images']) ?>" alt="<?= $car['make'] . ' ' . $car['model'] ?>" style="width: 200px; height: 140px;">
            </div>
            <button type="submit" name="update_car" class="btn btn-primary mb-2">Update Car</button>
        </form>
    </div>

    <!-- Include the footer -->
    <?php require_once "../includes/footer.php"; ?>
