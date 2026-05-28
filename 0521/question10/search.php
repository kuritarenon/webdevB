<?php
require_once "../functions.php";
$fp = fopen("songs.csv", "r");
var_dump($fp);
if ($fp === false) {
    echo "ファイルのオープンに失敗しました。";
    exit;
}

//formからの値取得
$keyword = $_POST["keyword"];
var_dump($keyword . "<br>");

while ($row = fgetcsv($fp)) {
    foreach ($row as $column) {
        if ($keyword === $column) {
            echo "曲名:" . $row[0] . "<br>";
            echo "アーティスト名：" . $row[1] . "<br>";
            echo "ジャンル：" . $row[2] . "<br>";
            echo "リリース年：" . $row[3] . "<br>";
            echo "メモ：" . $row[4] . "<br>";
        }
    }
}
