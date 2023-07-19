<?php

// Include the header
require_once "includes/header.php";
// Database connection
require_once "includes/db_connection.php";
// Determine the current page number (default to 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
// Number of cars to display per page
$carsPerPage = 5;
// Calculate the starting row for the current page
$startRow = ($page - 1) * $carsPerPage;

// Fetch cars data from the database with pagination
$sql = "SELECT * FROM cars LIMIT $startRow, $carsPerPage";
$result = $pdo->query($sql);
// Fetch all cars data into an array
$cars = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Car Listing Cards with Grid Layout -->
<div class="container pt-5">
  <div class="row car-list">
    <?php foreach ($cars as $car) : ?>
      <div class="col-md-4 col-sm-6 mb-4">
        <div class="card car-card">
          <!-- Car Image -->
          <?php
          $image_url = $car['images'] ? $car['images'] : 'https://placehold.co/200x200';
          ?>
          <img src="<?= $image_url ?>" class="card-img-top" alt="<?= $car['make'] . ' ' . $car['model'] ?>">

          <div class="card-body">
            <h2 class="card-title"><?= $car['make'] . ' ' . $car['model'] ?></h2>
            <p class="card-text">Year: <?= $car['year'] ?></p>
            <p class="card-text">Price: $<?= $car['price'] ?></p>
            <!-- Add more car details here -->
          </div>

          <!-- View Details Button -->
          <div class="card-footer text-center">
            <a href="car_details.php?id=<?= $car['id'] ?>" class="btn btn-primary">View Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <!-- Pagination links -->
  <div class="row">
    <div class="col">
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
          <?php
          $totalCars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
          $totalPages = ceil($totalCars / $carsPerPage);
          for ($i = 1; $i <= $totalPages; $i++):
          ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
  </div>
</div>
<!-- Include the footer -->
<?php require_once "includes/footer.php";?>
