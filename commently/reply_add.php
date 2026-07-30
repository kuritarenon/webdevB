<?php
// reply_add.php
require_once __DIR__ . '/inc/login_check.php';
require_once __DIR__ . '/inc/token_check.php';
require_once __DIR__ . '/inc/functions.php';

$postId  = (int)($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($postId > 0 && $content !== '') {
    $dbh = db_open();
    $sql = 'INSERT INTO replies (post_id, user_id, content) VALUES (:p, :u, :c)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':p', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':u', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':c', $content);
    $stmt->execute();
}
header('Location: index.php');
