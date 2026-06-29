<?php
$zip = $_POST['zip1'] . "-" . $_POST['zip2'];

if (preg_match("/^\d{3}-\d{4}$/", $zip)) {
    echo "正しい郵便番号です。";
} else {
    echo "郵便番号の形式が正しくありません。";
}
