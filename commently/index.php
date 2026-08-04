<?php
// index.php
session_start();
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';
$youtube = get_hikakin_movies();
?>
<article class="movie">
    <?php if (!empty($youtube['videos'])):
        $normalVideos = array_values(array_filter($youtube['videos'], function ($video) {
            $title = $video['title'] ?? '';
            $liveStatus = $video['liveBroadcastContent'] ?? '';

            // 配信予定・配信中のLIVEを除外
            if ($liveStatus === 'upcoming' || $liveStatus === 'live') {
                return false;
            }

            // APIから状態が取得できない場合はタイトルでも除外
            if (
                stripos($title, 'LIVE') !== false
                || stripos($title, 'ライブ') !== false
                || stripos($title, '配信') !== false
            ) {
                return false;
            }

            return true;
        }));

        // 通常動画がない場合は表示しない（LIVEを代わりに出さない）
        $latestVideo = $normalVideos[0] ?? null;
        if ($latestVideo) {
            $publishedAt = new DateTimeImmutable($latestVideo['published_at']);
        }
    ?>
        <div class="youtube-player" data-current-video-id="<?= str2html($latestVideo['id']) ?>">
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
            <?php foreach (array_slice($normalVideos, 1) as $video): ?>
                <li class="youtube-video-item">
                    <button type="button" class="youtube-thumbnail" data-video-id="<?= str2html($video['id']) ?>" data-title="<?= str2html($video['title']) ?>">
                        <img src="<?= str2html($video['thumbnail']) ?>" alt="<?= str2html($video['title']) ?>">
                        <p><?= str2html($video['title']) ?></p>
                    </button>
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

<section class="comments" data-video-id="<?= str2html($latestVideo['id']) ?>">
    <?php
    // index.php の <section class="comments"> の中身
    $dbh = db_open();

    $keyword = trim($_GET['keyword'] ?? '');
    $sort = $_GET['sort'] ?? 'latest';
    $orderBy = 'posts.created_at DESC';
    if ($sort === 'popular') {
        $orderBy = 'good_count DESC, posts.created_at DESC';
    }

    $where = '';
    $params = [];

    if ($keyword !== '') {
        $where = 'WHERE posts.content LIKE :keyword';
        $params[':keyword'] = '%' . $keyword . '%';
    }

    // 投稿一覧を、投稿者のユーザー情報と一緒に取得
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
    <form method="get" class="comment-search-form">
        <input type="text" name="keyword" value="<?= str2html($keyword) ?>" placeholder="コメントを検索">

        <button type="submit" class="search-btn">🔍 検索</button>

        <?php if ($keyword !== ''): ?>
            <a href="index.php" class="show-all-btn">すべてのコメント</a>
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
            <input type="hidden" name="keyword" value="<?= str2html($keyword) ?>">
            <select name="sort" onchange="this.form.submit()">
                <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>最新順</option>
                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>おすすめ順</option>
            </select>
        </form>
    </div>

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

                <?php if (!empty($_SESSION['login']) && (int)$_SESSION['user_id'] === (int)$post['user_id']): ?>
                    <form method="post" action="post_delete.php" class="inline"
                        onsubmit="return confirm('削除しますか？');">
                        <input type="hidden" name="token" value="<?= str2html($token) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                        <button type="submit">削除</button>
                    </form>
                    <button type="button" class="reply-toggle"
                        data-target="replies-<?= (int)$post['id'] ?>">
                        💬 返信<?= (int)$post['reply_count'] > 0 ? '（' . (int)$post['reply_count'] . '件）' : '' ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="reply-toggle"
                        data-target="replies-<?= (int)$post['id'] ?>">
                        💬 返信<?= (int)$post['reply_count'] > 0 ? '（' . (int)$post['reply_count'] . '件）' : '' ?>
                    </button>
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

    <?php if (!empty($_SESSION['login'])): ?>
        <div class="comment-footer">
            <button type="button" id="comment-toggle" class="comment-toggle">
                💬 コメントを書く
            </button>

            <form method="post"
                action="post_add.php"
                class="comment-form hidden"
                id="comment-form">

                <input type="hidden" name="token" value="<?= str2html($token) ?>">
                <input type="hidden" name="video_id" value="<?= str2html($latestVideo['id']) ?>">

                <textarea name="content" required placeholder="コメントを入力"></textarea>

                <button type="submit">投稿する</button>
            </form>
        </div>
    <?php else: ?>
        <div class="comment-footer">
            <p><a href="login.php">ログイン</a>するとコメントできます。</p>
        </div>
    <?php endif; ?>

</section>
<script>
    document.querySelectorAll('.youtube-thumbnail').forEach(button => {
        button.addEventListener('click', () => {
            const videoId = button.dataset.videoId;
            const title = button.dataset.title;
            const player = document.querySelector('.youtube-player iframe');
            const movieTitle = document.querySelector('.movie__title');

            player.src = `https://www.youtube-nocookie.com/embed/${videoId}`;
            player.title = title;
            movieTitle.textContent = title;

            const comments = document.querySelector('.comments');
            if (comments) {
                comments.dataset.videoId = videoId;
            }
            const videoInput = document.querySelector('input[name="video_id"]');
            if (videoInput) {
                videoInput.value = videoId;
            }
            const url = new URL(window.location.href);
            url.searchParams.set('video_id', videoId);
            window.location.href = url.toString();

            window.scrollTo({
                top: document.querySelector('.youtube-player').offsetTop,
                behavior: 'smooth'
            });
        });
    });
</script>
<?php include __DIR__ . '/inc/footer.php'; ?>