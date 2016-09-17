<?php
try {
    require_once '../../includes/mysqli_connect.php';
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MySQLi: Multiple Queries</title>
    <link href="../../styles/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1>MySQLi: Handling Multiple Queries</h1>
<?php if (isset($error)) {
    echo "<p>$error</p>";
} else {

}
$db->close();
?>
</body>
</html>