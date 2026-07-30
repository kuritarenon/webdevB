<?php
// index.php
session_start();
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';
$youtube = get_hikakin_movies();
?>
<article class="movie">
    <?php if (!empty($youtube['videos'])):
        $latestVideo = $youtube['videos'][0];
        $publishedAt = new DateTimeImmutable($latestVideo['published_at']);
    ?>
        <div class="youtube-player">
            <iframe src="https://www.youtube-nocookie.com/embed/<?= str2html($latestVideo['id']) ?>"
                title="<?= str2html($latestVideo['title']) ?>" allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
        </div>
        <h2 class="movie__title"><?= str2html($latestVideo['title']) ?></h2>
        <p class="movie__channel">公開日：<?= str2html($publishedAt->format('Y年n月j日')) ?></p>

        <section class="youtube-channel" aria-label="チャンネル情報">
            <?php if (!empty($youtube['channel']['icon'])): ?>
                <img src="<?= str2html($youtube['channel']['icon']) ?>" alt="<?= str2html($youtube['channel']['name']) ?> のアイコン">
            <?php endif; ?>
            <div>
                <a href="<?= str2html($youtube['channel']['url']) ?>" target="_blank" rel="noopener noreferrer">
                    <?= str2html($youtube['channel']['name']) ?>
                </a>
                <p>動画 <?= number_format($youtube['channel']['video_count']) ?> 本</p>
            </div>
        </section>

        <h3 class="movie__latest-heading">最新の投稿</h3>
        <ul class="youtube-video-list">
            <?php foreach (array_slice($youtube['videos'], 1) as $video): ?>
                <li>
                    <a href="https://www.youtube.com/watch?v=<?= str2html($video['id']) ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?= str2html($video['thumbnail']) ?>" alt="">
                        <span><?= str2html($video['title']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="youtube-unavailable">
            <p><?= str2html($youtube['error'] ?? 'YouTube 動画を取得できませんでした。') ?></p>
            <p>サーバーの環境変数 <code>YOUTUBE_API_KEY</code> に API キーを設定してください。</p>
        </div>
    <?php endif; ?>
</article>

<section class="comments">
    <?php
    // index.php の <section class="comments"> の中身
    $dbh = db_open();

    // 投稿一覧を、投稿者のユーザー情報と一緒に取得
    $sql = '
  SELECT posts.*, users.username, users.icon_path, users.color,
    (SELECT COUNT(*) FROM replies WHERE replies.post_id = posts.id) AS reply_count,
    (SELECT COUNT(*) FROM likes   WHERE likes.post_id   = posts.id AND type = "good") AS good_count
  FROM posts
  JOIN users ON posts.user_id = users.id
  ORDER BY posts.created_at DESC
';
    $posts = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $total = count($posts);

    // 一覧SELECTのすぐ後に、投稿ごとの返信を取得しておく
    $replySql = '
  SELECT replies.*, users.username, users.icon_path, users.color
  FROM replies
  JOIN users ON replies.user_id = users.id
  WHERE replies.post_id = :p
  ORDER BY replies.created_at ASC
';
    $replyStmt = $dbh->prepare($replySql);

    foreach ($posts as &$post) {
        $replyStmt->execute([':p' => $post['id']]);
        $post['replies'] = $replyStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($post);


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

                    <p class="comment__actions">
                        <?php if (!empty($_SESSION['login'])): ?>
                    <form method="post" action="reaction.php" class="inline">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <input type="hidden" name="type" value="good">
                        <button type="submit">👍 <?= (int)$post['good_count'] ?></button>
                    </form>
                    <form method="post" action="reaction.php" class="inline">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <input type="hidden" name="type" value="bad">
                        <button type="submit">👎</button>
                    </form>
                <?php else: ?>
                    <span>👍 <?= (int)$post['good_count'] ?></span>
                    <span>👎</span>
                <?php endif; ?>

                <?php if ((int)$post['reply_count'] > 0): ?>
                    <button type="button" class="reply-toggle"
                        data-target="replies-<?= (int)$post['id'] ?>">
                        <?= (int)$post['reply_count'] ?>件の返信を表示
                    </button>
                <?php endif; ?>

                <?php if (!empty($_SESSION['login']) && (int)$_SESSION['user_id'] === (int)$post['user_id']): ?>
                    <form method="post" action="post_delete.php" class="inline"
                        onsubmit="return confirm('削除しますか？');">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <button type="submit">削除</button>
                    </form>
                <?php endif; ?>
                </p>

                <div id="replies-<?= (int)$post['id'] ?>" class="replies hidden">
                    <ul class="reply-list">
                        <?php foreach ($post['replies'] as $r): ?>
                            <li class="reply">
                                <?= render_icon($r) ?>
                                <div>
                                    <p class="comment__meta">
                                        <strong>@<?= str2html($r['username']) ?></strong>
                                        <time><?= str2html($r['created_at']) ?></time>
                                    </p>
                                    <p class="comment__text"><?= nl2br(str2html($r['content'])) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($_SESSION['login'])): ?>
                        <form method="post" action="reply_add.php" class="reply-form">
                            <input type="hidden" name="token" value="<?= str2html($token) ?>">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <textarea name="content" required placeholder="返信を入力"></textarea>
                            <button type="submit">返信する</button>
                        </form>
                    <?php endif; ?>
                </div>
                </div>
            </li>

        <?php endforeach; ?>
    </ul>

</section>
<?php include __DIR__ . '/inc/footer.php'; ?>
