<?php
// inc/functions.php

/**
 * 開発環境用にプロジェクト直下の .env を読み込む。
 * 本番環境ではサーバー側の環境変数を優先する。
 */
function load_local_env(): void
{
    $envFile = dirname(__DIR__) . '/.env';
    if (!is_readable($envFile)) {
        return;
    }

    $values = parse_ini_file($envFile, false, INI_SCANNER_RAW);
    if (!is_array($values)) {
        return;
    }

    foreach ($values as $name => $value) {
        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

load_local_env();

const ICON_PALETTE = [
    '#e6749f',
    '#7ac74f',
    '#5aa9e6',
    '#f2b04b',
    '#a67c52',
    '#8a67e6',
    '#e6c247',
    '#4fd1c5',
];

function db_open()
{
    $user = 'phpuser';
    $password = 'krt0808nre'; // 任意のパスワード
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ];
    return new PDO('mysql:host=localhost;dbname=commently_db;charset=utf8mb4', $user, $password, $opt);
}

function str2html(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// アイコンをHTMLに変換する。$user は icon_path / color / username を含む配列
function render_icon(array $user): string
{
    if (!empty($user['icon_path'])) {
        return '<img class="user-icon" src="' . str2html($user['icon_path']) . '" alt="' . str2html($user['username']) . '">';
    }
    return '<span class="user-icon user-icon--color" style="background:' . str2html($user['color']) . '"></span>';
}

/**
 * YouTube Data API から HIKAKIN TV のチャンネル情報と最新動画を取得する。
 * API キーは Web 公開ディレクトリ外の環境変数 YOUTUBE_API_KEY に設定する。
 */
function get_hikakin_movies(int $limit = 6): array
{
    $apiKey = getenv('YOUTUBE_API_KEY');
    if (!$apiKey) {
        return ['error' => 'YouTube API キーが設定されていません。'];
    }

    // HIKAKIN TV の公式チャンネル ID
    $channelId = 'UCZf__ehlCEBPop-_sldpBUQ';
    $channelResponse = youtube_api_get('channels', [
        'part' => 'snippet,contentDetails,statistics',
        'id' => $channelId,
        'key' => $apiKey,
    ]);

    if (!empty($channelResponse['error']) || empty($channelResponse['items'][0])) {
        return ['error' => 'チャンネル情報を取得できませんでした。'];
    }

    $channel = $channelResponse['items'][0];
    $uploadsId = $channel['contentDetails']['relatedPlaylists']['uploads'] ?? '';
    if ($uploadsId === '') {
        return ['error' => '動画一覧を取得できませんでした。'];
    }

    $videoResponse = youtube_api_get('playlistItems', [
        'part' => 'snippet,contentDetails',
        'playlistId' => $uploadsId,
        'maxResults' => max(1, min($limit, 12)),
        'key' => $apiKey,
    ]);

    if (!empty($videoResponse['error'])) {
        return ['error' => '動画一覧を取得できませんでした。'];
    }

    return [
        'channel' => [
            'name' => $channel['snippet']['title'] ?? 'HIKAKIN TV',
            'icon' => $channel['snippet']['thumbnails']['medium']['url']
                ?? $channel['snippet']['thumbnails']['default']['url'] ?? '',
            'video_count' => (int)($channel['statistics']['videoCount'] ?? 0),
            'url' => 'https://www.youtube.com/channel/' . $channelId,
        ],
        'videos' => array_values(array_filter(array_map(static function (array $item): ?array {
            $snippet = $item['snippet'] ?? [];
            $videoId = $snippet['resourceId']['videoId'] ?? '';
            if ($videoId === '' || ($snippet['title'] ?? '') === 'Private video') {
                return null;
            }
            return [
                'id' => $videoId,
                'title' => $snippet['title'] ?? '',
                'published_at' => $snippet['publishedAt'] ?? '',
                'thumbnail' => $snippet['thumbnails']['medium']['url']
                    ?? $snippet['thumbnails']['default']['url'] ?? '',
            ];
        }, $videoResponse['items'] ?? []))),
    ];
}

/** YouTube Data API に GET リクエストを送る。 */
function youtube_api_get(string $resource, array $params): array
{
    $url = 'https://www.googleapis.com/youtube/v3/' . $resource . '?' . http_build_query($params);
    $body = false;

    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        $body = curl_exec($curl);
        curl_close($curl);
    } else {
        $body = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 10],
        ]));
    }

    $data = is_string($body) ? json_decode($body, true) : null;
    return is_array($data) ? $data : ['error' => ['message' => 'request failed']];
}
