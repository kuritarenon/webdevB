<?php
// inc/functions.php

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
