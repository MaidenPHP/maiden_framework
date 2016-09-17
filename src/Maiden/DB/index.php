<?php

include "db_connect_end.php";

// http://php.net/manual/en/class.pdostatement.php

$sql = "SELECT * FROM cars";
$cars = $db->query($sql);

/*var_export($cars); // iterator

foreach ($cars as $row) {
    echo $row['make_id'], '&nbsp;'. $row['description'], '<br>';
}*/

//var_dump($cars->fetch()); // iterator
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
