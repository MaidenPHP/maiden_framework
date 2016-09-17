<?php
try {
    require_once '../../includes/pdo_connect.php';
    $sql = 'DELETE FROM names WHERE name = "William"';
} catch (Exception $e) {
    $error = $e->getMessage();
}
if (isset($error)) {
    echo $error;
}