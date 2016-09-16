<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=persistent', 'sess_admin', 'secret');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $e->getMessage();
}

if (isset($db)) {
    echo 'Connected';
} elseif (isset($error)) {
    echo $error;
} else {
    echo 'Unknown error';
}