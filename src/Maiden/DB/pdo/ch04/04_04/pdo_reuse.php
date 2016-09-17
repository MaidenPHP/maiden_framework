<?php
try {
    require_once '../../includes/pdo_connect.php';
    $sql = 'SELECT name, meaning FROM names ORDER BY name';
    $result = $db->query($sql);
    $errorInfo = $db->errorInfo();
    if (isset($errorInfo[2])) {
        $error = $errorInfo[2];
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDO: Resetting the Cursor</title>
    <link rel="stylesheet" href="../../styles/styles.css" type="text/css">
</head>
<body>
<h1>PDO: Reusing a Result Set</h1>
<?php if (isset($error)) {
    echo "<p>$error</p>";
} else { ?>
<ul>
    <?php while ($row = $result->fetch()) {
        echo '<li><a href=#"' . $row['name'] . '">' . $row['name'] . '</a></li>';
    } ?>
</ul>
    <p>Lots more content here.</p>
    <dl>
        <?php while ($row = $result->fetch()) {
            echo '<dt id="' . $row['name'] . '">' . $row['name'] . '</dt>';
            echo '<dd>' . $row['meaning'] . '</dd>';
        } ?>
    </dl>
<?php } ?>
</body>
</html>