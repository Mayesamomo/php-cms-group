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

// Initialize variables to store form data and error messages
$make = "";
$model = "";
$year = "";
$price = "";
$quantity = "";
$images = "";
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form data
    $make = trim($_POST["make"]);
    $model = trim($_POST["model"]);
    $year = trim($_POST["year"]);
    $price = trim($_POST["price"]);
    $quantity = trim($_POST["quantity"]);
    $images = $_FILES["images"];

    // Validate form data
    if (empty($make) || empty($model) || empty($year) || empty($price) || empty($quantity) || empty($images)) {
        $error_message = "Please fill in all required fields.";
    } else {
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
            // Move the uploaded file to the images folder
            $target_file = $upload_dir . uniqid() . "." . $file_extension;
            if (move_uploaded_file($images["tmp_name"], $target_file)) {
                // Insert car details into the database
                $stmt = $pdo->prepare("
                    INSERT INTO cars (make, model, year, price, quantity, images)
                    VALUES (:make, :model, :year, :price, :quantity, :images)
                ");
                $stmt->execute([
                    'make' => $make,
                    'model' => $model,
                    'year' => $year,
                    'price' => $price,
                    'quantity' => $quantity,
                    'images' => $target_file,
                ]);

                // Redirect to the manage_cars.php page with success message
                $_SESSION['success_message'] = "Car successfully added.";
                header("Location: manage_cars.php");
                exit();
            } else {
                $error_message = "Error uploading image. Please try again.";
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
    <title>Admin - Add Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles.css" type="text/css" rel="stylesheet">
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
        <h2>Add Car</h2>
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
                <label for="images" class="form-label">Images</label>
                <input type="file" class="form-control" id="images" name="images" accept="image/*" required>
            </div>
            <?php if ($error_message) : ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Add Car</button>
        </form>
    </div>
    <?php require_once "../includes/footer.php"; ?>
