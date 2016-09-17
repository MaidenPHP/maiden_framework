<?php
try {
    require_once '../../includes/pdo_connect.php';
    $sql = 'SELECT name, meaning FROM names';
    $result = $db->query($sql);
    $names = $result->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
