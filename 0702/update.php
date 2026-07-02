<?php
require_once  __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/error_check.php';
include __DIR__ . '/inc/header.php';
// idのチェック
if (empty($_POST['id'])) {
    echo '<div class="message-card error">';
    echo '<div class="icon">⚠</div>';
    echo '<p class="msg">idを指定してください。</p>';
    echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
    echo '</div>';
    include __DIR__ . '/inc/fotter.php';
    exit;
}

//バリデーション（数字化どうか？）
if (!preg_match('/\A\d{0,11}+\z/u', $_POST['id'])) {
    echo '<div class="message-card error">';
    echo '<div class="icon">⚠</div>';
    echo '<p class="msg">idが正しくありません。</p>';
    echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
    echo '</div>';
    include __DIR__ . '/inc/fotter.php';
    exit;
}

try {
    $dbh = db_open();
    $sql = 'UPDATE books SET title = :title, isbn = :isbn, price = :price, publish = :publish, author = :author WHERE id = :id';
    $stmt = $dbh->prepare($sql);
    $id = (int)$_POST['id'];
    // bindParam() メソッドを使って、プレースホルダに値をバインドします。
    $stmt->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
    $stmt->bindParam(':isbn', $_POST['isbn'], PDO::PARAM_STR);
    $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_INT);
    $stmt->bindParam(':publish', $_POST['publish'], PDO::PARAM_STR);
    $stmt->bindParam(':author', $_POST['author'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    echo '<div class="message-card success">';
    echo '<div class="icon">✓</div>';
    echo '<p class="msg">データが更新されました。</p>';
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
