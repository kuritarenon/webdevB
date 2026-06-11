<?php
//api4.php
$rtn = preg_match('/\A\d{7}\z/u', $_GET["zip"]);
if (!$rtn) {
    echo "郵便番号は7桁の数字を入力してください";
    exit;
}
$url = "https://zipcloud.ibsnet.co.jp/api/search?zipcode=" . $_GET["zip"];
$response = file_get_contents($url);
$response = json_decode($response, true);

echo "入力された郵便番号は";
echo $response['results'][0]['address1'];
echo $response['results'][0]['address2'];
echo $response['results'][0]['address3'];
echo "の郵便番号です。";
echo $response['message'];
