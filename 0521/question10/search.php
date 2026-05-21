<?php
require_once "../functions.php";
$fp = fopen("songs.csv", "r");
var_dump($fp);
if ($fp === false) {
    echo "ファイルのオープンに失敗しました。";
    exit;
}
