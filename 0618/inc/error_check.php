<?php
function show_error_and_exit(string $message) {
    include_once __DIR__ . '/header.php';
    echo '<div class="message-card error">';
    echo '<div class="icon">⚠</div>';
    echo '<p class="msg">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p class="back-link"><a href="javascript:history.back()">戻る</a></p>';
    echo '</div>';
    include_once __DIR__ . '/fotter.php';
    exit;
}

# 空欄チェック
if (empty($_POST['title'])) {
    show_error_and_exit('タイトルは必須です。');
}
# 文字数チェック
if (!preg_match('/\A[[:^cntrl:]]{1,200}\z/u', $_POST['title'])) {
    show_error_and_exit('タイトルは200文字までです。');
}

# ISBN（13桁)チェック
if (!preg_match('/\A\d{0,13}\z/u', $_POST['isbn'])) {
    show_error_and_exit('ISBNは数字13桁までです。');
}

# 定価（6桁）チェック
if (!preg_match('/\A\d{0,6}\z/u', $_POST['price'])) {
    show_error_and_exit('定価は数字6桁までです。');
}
# 日付必須チェック
if (empty($_POST['publish'])) {
    show_error_and_exit('日付は必須です。');
}
# 出版日（yyyy-mm-dd）チェック
if (!preg_match('/\A\d{4}-\d{1,2}-\d{1,2}\z/u', $_POST['publish'])) {
    show_error_and_exit('日付のフォーマットが違います。');
}
# 正しい日付チェック
$date = explode('-', $_POST['publish']);
if (!checkdate($date[1], $date[2], $date[0])) {
    show_error_and_exit('正しい日付を入力してください。');
}
# 著者（80文字）チェック
if (!preg_match('/\A[[:^cntrl:]]{1,80}\z/u', $_POST['author'])) {
    show_error_and_exit('著者名は80文字以内で入力してください。');
}
