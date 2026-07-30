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
    <?php
    // index.php の <section class="comments"> の中身
    $dbh = db_open();

    // 投稿一覧を、投稿者のユーザー情報と一緒に取得
    $sql = '
  SELECT posts.*, users.username, users.icon_path, users.color
  FROM posts
  JOIN users ON posts.user_id = users.id
  ORDER BY posts.created_at DESC
';
    $posts = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $total = count($posts);

    // CSRFトークン（投稿フォーム用）
    $token = bin2hex(random_bytes(20));
    $_SESSION['token'] = $token;
    ?>
    <h2 class="comments__count"><?= $total ?>件のコメント</h2>

    <?php if (!empty($_SESSION['login'])): ?>
        <form method="post" action="post_add.php" class="comment-form">
            <input type="hidden" name="token" value="<?= str2html($token) ?>">
            <textarea name="content" required placeholder="コメントを入力"></textarea>
            <button type="submit">投稿する</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">ログイン</a>するとコメントできます。</p>
    <?php endif; ?>

    <ul class="comment-list">
        <?php foreach ($posts as $post): ?>
            <li class="comment">
                <?= render_icon($post) ?>
                <div class="comment__body">
                    <p class="comment__meta">
                        <strong>@<?= str2html($post['username']) ?></strong>
                        <time><?= str2html($post['created_at']) ?></time>
                    </p>
                    <p class="comment__text"><?= nl2br(str2html($post['content'])) ?></p>
                    <?php if (!empty($_SESSION['login']) && (int)$_SESSION['user_id'] === (int)$post['user_id']): ?>
                        <p class="comment__actions">
                        <form method="post" action="post_delete.php" class="inline"
                            onsubmit="return confirm('削除しますか？');">
                            <input type="hidden" name="token" value="<?= str2html($token) ?>">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <button type="submit">削除</button>
                        </form>
                        </p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

</section>
<?php include __DIR__ . '/inc/footer.php'; ?>