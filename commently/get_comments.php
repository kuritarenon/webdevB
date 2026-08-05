<?php
// get_comments.php
session_start();
require_once __DIR__ . '/inc/functions.php';

$latestVideoId = trim($_GET['video_id'] ?? '');
if ($latestVideoId === '') {
    http_response_code(400);
    echo 'Video ID is required.';
    exit;
}

include __DIR__ . '/inc/comments_section.php';
