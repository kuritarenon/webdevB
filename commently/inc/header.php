<?php
// inc/header.php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MovieApp</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <header class="site-header">
        <h1 class="site-logo">MovieApp</h1>
        <nav class="site-nav">
            <?php if (!empty($_SESSION['login']) && !empty($_SESSION['username'])): ?>
                <span>こんにちは、@<?= str2html($_SESSION['username']) ?> さん</span>
                <a href="logout.php">ログアウト</a>
            <?php else: ?>
                <a href="login.php">ログイン</a>
                <a href="register.php">登録</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="site-main">