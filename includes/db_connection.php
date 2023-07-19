<?php
// Database connection using PDO instead of mysqli, because of for its flexibility and security features.
try {
    $db_host = "localhost";
    $db_name = "car_depo";
    $db_user = "root";
    $db_pass = "";

    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
