<?php

// http://php.net/manual/en/class.pdostatement.php

$sql = "SELECT * FROM cars ORDER BY price DESC";
$cars = $db->query($sql);

// returns integer - 1 row is affected
//$db->exec("INSERT INTO cars (column1,column2) VALUE (v1, v2)");
//$db->exec("UPDATE cars SET col1 = '' WHERE 'name' = 'benz'");
//$db->exec("DELETE FROM cars WHERE 'name' = 'benz'");

prettyView($cars->fetch(4)); // iterator

/*foreach ($cars as $row) {
    echo $row['make_id'], '&nbsp;'. $row['description'], '<br>';
}*/

//prettyView($cars->fetch()); // iterator
//var_dump($cars->fetchObject());
//var_dump($cars->fetchAll(PDO::FETCH_CLASS));
//var_dump($cars->fetchColumn());

//var_dump($cars->rowCount());
//var_dump($cars->nextRowset());


/*
 * Iterator - an object that enables you to traverse a contaienr
 * using a foreach loop over a collection
 *
 * why use an iterator
 * - faster and less memory intensive
 *
 * stacking iterators
 *
 *
 *
 * current
 * key
 * next
 * rewind
 * valid
 *
 */

