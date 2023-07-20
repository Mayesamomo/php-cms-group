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

// Check if the user clicked the "Delete" button for a specific car
if (isset($_POST['delete_car'])) {
    // Get the car ID from the form submission
    $car_id = intval($_POST['car_id']);

    // Delete the car from the database
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = :car_id");
    $result = $stmt->execute(['car_id' => $car_id]);

    // Set a session variable with the success/error message
    if ($result) {
        $_SESSION['success_message'] = "Car successfully deleted.";
    } else {
        $_SESSION['error_message'] = "Error occurred while deleting the car.";
    }

    // Redirect to the same page to avoid resubmission on refresh
    header("Location: manage_cars.php");
    exit();
}

// Fetch cars data from the database
$stmt = $pdo->prepare("SELECT * FROM cars");
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Manage Cars - Car Depot</title>
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
            <a class="btn btn-success" href="add_car.php">Add Car</a>
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

    <h2>Manage Cars</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Make</th>
            <th>Model</th>
            <th>Year</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cars as $car) : ?>
            <tr>
                <td><?= htmlspecialchars($car['make']) ?></td>
                <td><?= htmlspecialchars($car['model']) ?></td>
                <td><?= $car['year'] ?></td>
                <td>$<?= number_format($car['price'], 2) ?></td>
                <td><?= $car['quantity'] ?></td>
                <td><img src="<?= htmlspecialchars($car['images']) ?>" alt="<?= $car['make'] . ' ' . $car['model'] ?>" style="width: 100px; height: 70px;"></td>
                <td>
                    <form method="post" action="manage_cars.php">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <button type="submit" name="delete_car" class="btn btn-danger">Delete</button>
                    </form>
                    <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-primary">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Include the footer -->
<?php require_once "../includes/footer.php"; ?>

