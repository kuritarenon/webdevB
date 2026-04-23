<?php
$a = $_POST['a'];
if ($a === '1') {
    echo "aは1です。";
} elseif ($a === '2') {
    echo "aは2です。";
} else {
    echo "aは1でも2でもありません。";
}
// if ($a !== '1') {
//     echo "aは1ではありません。";
// }
