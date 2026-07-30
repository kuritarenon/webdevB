<?php
// index.php
session_start();
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';
?>
<article class="movie">
    <video src="videos/sample.mp4" controls poster="videos/sample.jpg"></video>
    <h2 class="movie__title">あんなこんなを撮ってみた パート1</h2>
    <p class="movie__channel">annakona.TV</p>
</article>

<section class="comments">
    <h2 class="comments__count">0件のコメント</h2>
    <!-- ステップ②でここに投稿フォームと一覧を追加 -->
</section>
<?php include __DIR__ . '/inc/footer.php'; ?>