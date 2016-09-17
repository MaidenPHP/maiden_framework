<?php
try {
    require_once '../../includes/pdo_connect.php';
    require_once 'Car.php';

    // Create an instance of Car object
    $car_id = 18;
    $car = new Car($car_id);

    $sql = 'SELECT * FROM cars
            LEFT JOIN makes USING (make_id)
            WHERE car_id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute(array($car_id));
    $stmt->setFetchMode(PDO::FETCH_INTO, $car);
    $stmt->fetch();

    // Display the car object
    echo $car;
} catch (Exception $e) {
    $error = $e->getMessage();
}
if (isset($error)) {
    echo $error;
}