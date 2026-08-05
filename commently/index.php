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
            $liveStatus = $video['liveBroadcastContent'] ?? 'none';
            if ($liveStatus === 'upcoming' || $liveStatus === 'live') {
                return false;
            }
            return true;
        }));

        $selectedVideoId = trim($_GET['video_id'] ?? '');
        $latestVideo = null;

        if (!empty($normalVideos)) {
            if ($selectedVideoId !== '') {
                foreach ($normalVideos as $v) {
                    if ($v['id'] === $selectedVideoId) {
                        $latestVideo = $v;
                        break;
                    }
                }
            }
            if (!$latestVideo) {
                $latestVideo = $normalVideos[0];
            }
        }

        if ($latestVideo) {
            $publishedAt = new DateTimeImmutable($latestVideo['published_at']);
        }
    ?>
        <?php if ($latestVideo): ?>
            <div class="youtube-player" data-current-video-id="<?= str2html($latestVideo['id']) ?>">
                <iframe src="https://www.youtube-nocookie.com/embed/<?= str2html($latestVideo['id']) ?>"
                    title="<?= str2html($latestVideo['title']) ?>" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
            <h2 class="movie__title"><?= str2html($latestVideo['title']) ?></h2>
            <p class="movie__channel" id="movie-published-at">公開日：<?= str2html($publishedAt->format('Y年n月j日')) ?></p>

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

            <h3 class="movie__latest-heading">他の動画</h3>
            <ul class="youtube-video-list">
                <?php 
                // 現在の再生中動画以外の動画から最大4件を取得（無ければ全体から4件）
                $otherVideos = array_values(array_filter($normalVideos, function($v) use ($latestVideo) {
                    return $v['id'] !== $latestVideo['id'];
                }));
                $displayVideos = array_slice($otherVideos, 0, 4);
                if (empty($displayVideos)) {
                    $displayVideos = array_slice($normalVideos, 0, 4);
                }
                foreach ($displayVideos as $video): 
                    $vDate = new DateTimeImmutable($video['published_at']);
                    $vDateStr = $vDate->format('Y年n月j日');
                    $isActive = ($video['id'] === $latestVideo['id']);
                ?>
                    <li class="youtube-video-item">
                        <button type="button" 
                            class="youtube-thumbnail <?= $isActive ? 'is-active' : '' ?>" 
                            data-video-id="<?= str2html($video['id']) ?>" 
                            data-title="<?= str2html($video['title']) ?>"
                            data-published="<?= str2html($vDateStr) ?>">
                            <div class="youtube-thumbnail__image-wrapper">
                                <img src="<?= str2html($video['thumbnail']) ?>" alt="<?= str2html($video['title']) ?>" loading="lazy">
                                <span class="youtube-thumbnail__play-badge">
                                    <?= $isActive ? '▶ 再生中' : '▶' ?>
                                </span>
                            </div>
                            <div class="youtube-thumbnail__info">
                                <p class="youtube-thumbnail__title"><?= str2html($video['title']) ?></p>
                                <span class="youtube-thumbnail__date"><?= str2html($vDateStr) ?></span>
                            </div>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="youtube-unavailable">
                <p>表示できる通常の動画がありませんでした。</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="youtube-unavailable">
            <p><?= str2html($youtube['error'] ?? 'YouTube 動画を取得できませんでした。') ?></p>
            <p>サーバーの環境変数 <code>YOUTUBE_API_KEY</code> に API キーを設定してください。</p>
        </div>
    <?php endif; ?>
</article>

<section class="comments" data-video-id="<?= str2html($latestVideo['id'] ?? '') ?>">
    <?php 
    $latestVideoId = $latestVideo['id'] ?? '';
    include __DIR__ . '/inc/comments_section.php'; 
    ?>
</section>

<script>
    document.querySelectorAll('.youtube-thumbnail').forEach(button => {
        button.addEventListener('click', () => {
            const videoId = button.dataset.videoId;
            const title = button.dataset.title;
            const published = button.dataset.published;
            const player = document.querySelector('.youtube-player iframe');
            const movieTitle = document.querySelector('.movie__title');
            const movieDate = document.getElementById('movie-published-at');

            if (player) {
                player.src = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
                player.title = title;
            }
            if (movieTitle) {
                movieTitle.textContent = title;
            }
            if (movieDate && published) {
                movieDate.textContent = `公開日：${published}`;
            }

            document.querySelectorAll('.youtube-thumbnail').forEach(btn => {
                const badge = btn.querySelector('.youtube-thumbnail__play-badge');
                if (btn.dataset.videoId === videoId) {
                    btn.classList.add('is-active');
                    if (badge) badge.textContent = '▶ 再生中';
                } else {
                    btn.classList.remove('is-active');
                    if (badge) badge.textContent = '▶';
                }
            });

            // 動画に応じたコメント欄を動的非同期取得
            fetch(`get_comments.php?video_id=${encodeURIComponent(videoId)}`)
                .then(res => res.text())
                .then(html => {
                    const commentsSection = document.querySelector('.comments');
                    if (commentsSection) {
                        commentsSection.dataset.videoId = videoId;
                        commentsSection.innerHTML = html;
                        if (typeof initCommentHandlers === 'function') {
                            initCommentHandlers();
                        }
                    }
                })
                .catch(err => console.error('コメントの読み込みに失敗しました:', err));

            const url = new URL(window.location.href);
            url.searchParams.set('video_id', videoId);
            window.history.pushState({ videoId }, title, url.toString());

            const playerContainer = document.querySelector('.youtube-player');
            if (playerContainer) {
                playerContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
</script>
<?php include __DIR__ . '/inc/footer.php'; ?>