<?php
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/error_check.php';
include __DIR__ . '/inc/header.php';

try {
    $dbh = db_open();
    $sql = "INSERT INTO books (id, title, isbn, price, publish, author)
VALUES (NULL, :title, :isbn, :price, :publish, :author)";

    # prepare() メソッドを使って、SQL文を準備します。
    $stmt = $dbh->prepare($sql);

    $price = (int)$_POST['price'];
    $stmt->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
    $stmt->bindParam(':isbn', $_POST['isbn'], PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':publish', $_POST['publish'], PDO::PARAM_STR);
    $stmt->bindParam(':author', $_POST['author'], PDO::PARAM_STR);

    $stmt->execute();
    echo '<div class="message-card success">';
    echo '<div class="icon">✓</div>';
    echo '<p class="msg">データが追加されました。</p>';
    echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
    echo '</div>';
} catch (PDOException $e) {
    echo '<div class="message-card error">';
    echo '<div class="icon">⚠</div>';
    echo '<p class="msg">エラーが発生しました: ' . str2html($e->getMessage()) . '</p>';
    echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
    echo '</div>';
    exit;
}

include __DIR__ . '/inc/fotter.php';
