<?php
require_once 'data.php';
// var_dump($people);
foreach ($people as $key => $person) {
    echo '名前は、' . $person['name'] . "<br>";
}
