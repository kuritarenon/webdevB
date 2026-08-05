<?php
// post_add.php
require_once __DIR__ . '/inc/login_check.php';
require_once __DIR__ . '/inc/token_check.php';
require_once __DIR__ . '/inc/functions.php';

$content = trim($_POST['content'] ?? '');
$videoId = trim($_POST['video_id'] ?? '');

if ($content !== '' && $videoId !== '') {
    $dbh = db_open();
    $sql = 'INSERT INTO posts (user_id, video_id, content) VALUES (:u, :v, :c)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':u', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':v', $videoId);
    $stmt->bindValue(':c', $content);
    $stmt->execute();
}

$redirectUrl = 'index.php';
if ($videoId !== '') {
    $redirectUrl .= '?video_id=' . urlencode($videoId);
}
header('Location: ' . $redirectUrl);

