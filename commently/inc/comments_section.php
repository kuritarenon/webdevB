<?php
// inc/comments_section.php
if (!isset($latestVideoId) || $latestVideoId === '') {
    $latestVideoId = trim($_GET['video_id'] ?? '');
}

$dbh = db_open();

$keyword = trim($_GET['keyword'] ?? '');
$sort = $_GET['sort'] ?? 'latest';
$orderBy = 'posts.created_at DESC';
if ($sort === 'popular') {
    $orderBy = 'good_count DESC, posts.created_at DESC';
}

$whereClauses = ['posts.video_id = :v'];
$params = [':v' => $latestVideoId];

if ($keyword !== '') {
    $whereClauses[] = 'posts.content LIKE :k';
    $params[':k'] = '%' . $keyword . '%';
}

$where = 'WHERE ' . implode(' AND ', $whereClauses);

$sql = '
  SELECT posts.*, users.username, users.icon_path, users.color,
    (SELECT COUNT(*) FROM replies WHERE replies.post_id = posts.id) AS reply_count,
    (SELECT COUNT(*) FROM likes   WHERE likes.post_id   = posts.id AND type = "good") AS good_count
  FROM posts
  JOIN users ON posts.user_id = users.id
  ' . $where . '
  ORDER BY ' . $orderBy . '
';
$stmt = $dbh->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($posts);

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

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(20));
}
$token = $_SESSION['token'];
?>

<form method="get" class="comment-search-form">
    <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
    <input type="text" name="keyword" value="<?= str2html($keyword) ?>" placeholder="コメントを検索">

    <button type="submit" class="search-btn">🔍 検索</button>

    <?php if ($keyword !== ''): ?>
        <a href="index.php?video_id=<?= str2html($latestVideoId) ?>" class="show-all-btn">すべてのコメント</a>
    <?php endif; ?>
</form>

<?php if ($keyword !== ''): ?>
    <?php if ($total > 0): ?>
        <p class="search-result">
            「<strong><?= str2html($keyword) ?></strong>」の検索結果：<?= $total ?>件
        </p>
    <?php else: ?>
        <p class="search-result no-result">
            「<strong><?= str2html($keyword) ?></strong>」に一致するコメントはありませんでした。
        </p>
    <?php endif; ?>
<?php endif; ?>

<div class="comments-header">
    <h2 class="comments__count"><?= $total ?>件のコメント</h2>

    <form method="get" class="sort-form">
        <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
        <input type="hidden" name="keyword" value="<?= str2html($keyword) ?>">
        <select name="sort" onchange="this.form.submit()">
            <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>最新順</option>
            <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>おすすめ順</option>
        </select>
    </form>
</div>

<ul class="comment-list">
    <?php if (empty($posts)): ?>
        <li class="comment-empty">
            <p>この動画にはまだコメントがありません。最初のコメントを投稿してみましょう！</p>
        </li>
    <?php else: ?>
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
                        <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <input type="hidden" name="type" value="good">
                        <button type="submit">👍 <?= (int)$post['good_count'] ?></button>
                    </form>
                    <form method="post" action="reaction.php" class="inline">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <input type="hidden" name="type" value="bad">
                        <button type="submit">👎</button>
                    </form>
                <?php else: ?>
                    <span>👍 <?= (int)$post['good_count'] ?></span>
                    <span>👎</span>
                <?php endif; ?>

                <?php if (!empty($_SESSION['login']) && (int)$_SESSION['user_id'] === (int)$post['user_id']): ?>
                    <form method="post" action="post_delete.php" class="inline"
                        onsubmit="return confirm('削除しますか？');">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <button type="submit">削除</button>
                    </form>
                <?php endif; ?>

                <button type="button" class="reply-toggle"
                    data-target="replies-<?= (int)$post['id'] ?>">
                    💬 返信<?= (int)$post['reply_count'] > 0 ? '（' . (int)$post['reply_count'] . '件）' : '' ?>
                </button>
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
                            <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <textarea name="content" required placeholder="返信を入力"></textarea>
                            <button type="submit">返信する</button>
                        </form>
                    <?php endif; ?>
                </div>
                </div>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<?php if (!empty($_SESSION['login'])): ?>
    <div class="comment-footer">
        <button type="button" id="comment-toggle" class="comment-toggle">
            💬 コメントを書く
        </button>

        <form method="post"
            action="post_add.php"
            class="comment-form hidden"
            id="comment-form">

            <button type="button" id="comment-close-icon" class="comment-form__close-icon" title="閉じる" aria-label="閉じる">×</button>

            <input type="hidden" name="token" value="<?= str2html($token) ?>">
            <input type="hidden" name="video_id" value="<?= str2html($latestVideoId) ?>">

            <textarea name="content" required placeholder="コメントを入力"></textarea>

            <div class="comment-form__actions">
                <button type="submit" class="comment-form__submit-btn">投稿する</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="comment-footer">
        <p><a href="login.php">ログイン</a>するとコメントできます。</p>
    </div>
<?php endif; ?>