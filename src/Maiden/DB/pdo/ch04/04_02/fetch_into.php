<?php
try {
    require_once '../../includes/pdo_connect.php';
    require_once 'Car.php';

    // Create an instance of Car object
    $car_id = 10;
    $car = new Car($car_id);

    // Display the car object
    echo $car;
} catch (Exception $e) {
    $error = $e->getMessage();
}
if (isset($error)) {
    echo $error;
}