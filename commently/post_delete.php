<?php
// post_delete.php
require_once __DIR__ . '/inc/login_check.php';
require_once __DIR__ . '/inc/token_check.php';
require_once __DIR__ . '/inc/functions.php';

$postId = (int)($_POST['post_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if ($postId > 0) {
    try {
        $dbh = db_open();
        $dbh->beginTransaction();
        // 関連する返信・リアクションを先に消す
        $dbh->prepare('DELETE FROM replies WHERE post_id = :p')->execute([':p' => $postId]);
        $dbh->prepare('DELETE FROM likes   WHERE post_id = :p')->execute([':p' => $postId]);
        // 自分の投稿だけ消せる
        $stmt = $dbh->prepare('DELETE FROM posts WHERE id = :id AND user_id = :u');
        $stmt->execute([':id' => $postId, ':u' => $userId]);
        $dbh->commit();
    } catch (PDOException $e) {
        $dbh->rollBack();
    }
}
header('Location: index.php');
