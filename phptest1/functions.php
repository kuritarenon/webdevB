<!-- functions.phpに定義された関数 greet() を読み込んで実行するコードを書いてください。エラーが出たら処理を停止させるようにしてください。-->
<?php
// functions.php
function str2html(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
function greet()
{
    echo "<p>こんにちは！</p>";
}
