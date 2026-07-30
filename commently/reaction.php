<?php
// reaction.php
require_once __DIR__ . '/inc/login_check.php';
require_once __DIR__ . '/inc/token_check.php';
require_once __DIR__ . '/inc/functions.php';

$postId = (int)($_POST['post_id'] ?? 0);
$type   = $_POST['type'] ?? '';
$userId = (int)$_SESSION['user_id'];

if ($postId > 0 && in_array($type, ['good', 'bad'], true)) {
    $dbh = db_open();
    $sel = $dbh->prepare('SELECT id, type FROM likes WHERE post_id = :p AND user_id = :u');
    $sel->execute([':p' => $postId, ':u' => $userId]);
    $row = $sel->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // まだリアクションなし → INSERT
        $sql = 'INSERT INTO likes (post_id, user_id, type) VALUES (:p, :u, :t)';
        $dbh->prepare($sql)->execute([':p' => $postId, ':u' => $userId, ':t' => $type]);
    } elseif ($row['type'] === $type) {
        // 同じボタンをもう一度 → DELETE（取り消し）
        $dbh->prepare('DELETE FROM likes WHERE id = :id')
            ->execute([':id' => (int)$row['id']]);
    } else {
        // 反対のリアクション → UPDATE（切替）
        $dbh->prepare('UPDATE likes SET type = :t WHERE id = :id')
            ->execute([':t' => $type, ':id' => (int)$row['id']]);
    }
}
header('Location: index.php');
