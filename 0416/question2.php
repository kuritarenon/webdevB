<?php
# 変数 $first = "Hello" と $second = "World" を使って、1つの文字列に結合して出力してください。
$first = "Hello";
$second = "World";
echo $first . $second;
echo "<br>";

# 変数 $name = "Taro" を使って、「こんにちは、Taroさん」 と表示されるように、文字列を結合して出力してください。
$name = "Taro";
echo "こんにちは、" . $name . "さん";
echo "<br>";

# 変数 $greeting = "こんにちは" に 「、元気ですか？」 を「.=」を使って追加し、結果を表示してください。
$greeting = "こんにちは";
$greeting .= "、元気ですか？";
echo $greeting;
echo "<br>";

# 文字列 "PHP"、" is "、"fun!" をそれぞれ変数に入れて、すべて結合して出力するコードを書いてください。
$a = "PHP";
$b = "is";
$c = "fun!";
echo $a . $b . $c;
echo "<br>";

# 変数 $text = "" を初期化し、"A", "B", "C" を順に .= で追加して出力してください。
$text = "";
$text .= "A";
$text .= "B";
$text .= "C";
echo $text;
