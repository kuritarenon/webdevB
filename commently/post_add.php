<?php
// post_add.php
require_once __DIR__ . '/inc/login_check.php';
require_once __DIR__ . '/inc/token_check.php';
require_once __DIR__ . '/inc/functions.php';

$content = trim($_POST['content'] ?? '');
if ($content !== '') {
    $dbh = db_open();
    $sql = 'INSERT INTO posts (user_id, content) VALUES (:u, :c)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':u', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':c', $content);
    $stmt->execute();
}
header('Location: index.php');
