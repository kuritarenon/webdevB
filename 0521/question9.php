<?php
// 問題1：「Hello, World!」を出力する関数を定義して、実行してください。
// 期待される出力：Hello, World!
function greet(string $hi)
{
    echo $hi;
}

greet("Hello, World!");
// 問題2：数字を1つ受け取り、その2倍の値を返す関数を定義し、10を渡して結果を表示してください。
// 期待される出力：20
function num(int $n)
{
    echo $n * 2;
};
num(10);
// 問題3：名前を引数にとり、「こんにちは、〇〇さん」と表示する関数を作成してください（〇〇に「Taro」を指定）。
// 期待される出力：こんにちは、Taroさん
function messege(string $a)
{
    echo "こんにちは、" . $a . "さん";
}

messege("Taro");

// 問題4：2つの数値を受け取り、合計を返す関数を定義し、5と7を渡して結果を表示してください。
// 期待される出力：12
function sum(int $num1, int $num2)
{
    echo $num1 + $num2;
}
sum(5, 7);

// 問題5：引数が偶数かどうかを判定し、true/falseを返す関数を作ってください（4を渡す）。
// 期待される出力：1（true）
function check(int $value)
{
    if ($value % 2 == 0) {
        echo "true";
    } else {
        echo "false";
    }
};

check(4);

// 問題6：3つの引数を受け取り、最大値を返す関数を作成し、3, 9, 7 を渡して実行してください。
// 期待される出力：9
function maxnum(int $b, int $c, int $d)
{
    echo max($b, $c, $d);
}

maxnum(3, 9, 7);

// 問題7：配列を引数として受け取り、すべての要素の合計を返す関数を定義してください。
// 期待される出力：10
$numbers = [1, 2, 3, 4];
$sum = 0;
function total(array $array)
{
    foreach ($array as $number) {
        $sum += $number;
    }
    return $sum;
}

echo total($numbers);

// 問題8：文字列を大文字に変換して返す関数を定義し、「php」を渡してください。
// 期待される出力：PHP
function henkan(string $text)
{
    echo mb_strtoupper($text);
}

henkan("php");

// 問題9：数字を受け取り、偶数なら「Even」、奇数なら「Odd」と返す関数を作ってください（3を渡す）。
// 期待される出力：Odd
function hantei(int $e)
{
    if ($e % 2 == 0) {
        echo "Even";
    } else {
        echo "Odd";
    }
};

hantei(3);

// 問題10：名前と年齢を引数に取り、「〇〇さんは△△歳です」と表示する関数を作ってください（Yuki, 25）
// 期待される出力：Yukiさんは25歳です
function person(string $name, int $age)
{
    echo $name . "さんは" . $age . "歳です";
}
person("Yuki", 25);
