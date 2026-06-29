<!-- 以下のフォームから送信された値（name と comment）をPHPで受け取り、
〇〇さんのコメント：〇〇
と表示するコードを書いてください。 -->
<?php
$name = $_POST['name'];
$comment = $_POST['comment'];

echo $name . "さんのコメント：" . $comment;
