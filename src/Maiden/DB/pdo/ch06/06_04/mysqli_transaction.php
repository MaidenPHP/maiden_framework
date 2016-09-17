<?php
try {
    require_once '../../includes/mysqli_connect.php';
    $balances = $db->query('SELECT name, balance FROM savings');
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MySQLi Transaction</title>
    <link href="../../styles/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1>MySQLi Transactions</h1>
<?php
if (isset($error)) {
    echo "<p>$error</p>";
}
?>
<table>
    <tr>
        <th>Name</th>
        <th>Balance</th>
    </tr>
    <?php while ($row = $balances->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td>$<?php echo number_format($row['balance'], 2); ?></td>
    </tr>
    <?php } ?>
</table>
<?php $db->close(); ?>
</body>
</html>