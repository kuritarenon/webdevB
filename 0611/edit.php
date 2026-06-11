<?php
require_once 'functions.php';
if (empty($_GET['id'])) {
    echo "idを指定してください";
    exit;
}
if (!preg_match('/\A\d{1,11}+\z/u', $_GET['id'])) {
    echo "idが正しくありません。";
    exit;
}
$id = (int)$_GET['id'];
//var_dump($id);

$dbh = db_open();
$sql = "SELECT id,title,isbn,price,publish,author FROM books WHERE id=:id";
$stml = $dbh->prepare($sql);
$stml->bindParam(":id", $id, PDO::PARAM_INT);
$stml->execute();
$result = $stml->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    echo "指定したデータはありません。";
    exit;
}
var_dump($result);
