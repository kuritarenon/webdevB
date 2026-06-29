<!-- 連想配列 $person = ['name' => 'Taro', 'age' => 20] のキーと値を「name: Taro」の形式で出力するコードをforeachを使って書いてください。
$person = ['name' => 'Taro', 'age' => 20] ; -->

<?php
$person = ['name' => 'Taro', 'age' => 20];

foreach ($person as $key => $value) {
    echo $key . ": " . $value . "<br>";
}
?>