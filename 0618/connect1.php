<?php
require_once 'functions.php';

try {
    $dbh = db_open();
    $sql = 'SELECT title, author FROM books';
    # PDOStatementオブジェクトを取得（宣言）
    $statment = $dbh->query($sql);

    # PDOStatementオブジェクトに定着されたfetchメソッドで値を取得
    while ($row = $statment->fetch()) {
        echo '書籍名：' . str2html($row[0]) . '<br>';
        echo '著者名：' . str2html($row[1]) . '<br><br>';
    }
} catch (PDOException $e) {
    echo 'エラー！: ' . str2html($e->getMessage()) . '<br>';
    exit;
}
