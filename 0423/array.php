<?php
$a = ["田中", "井上"];
echo $a[0];

$name = [
    'sato' => '佐藤',
    'suzuki' => '鈴木',
    'takahashi' => '高橋'
];
echo $name['takahashi'];

// var_dumpは値を全部表示する
// var_dump($name);

// 自動的に要素の数だけループする
// ループすると、配列の要素がそれぞれのasの後の変数に代入される
// 処理がループの数だけ実行される
foreach ($name as $value) {
    echo '名前は' . $value . '<br>';
}

// キーを扱う場合の書き方
foreach ($name as $key => $value) {
    echo 'キーは' . $key . '、名前は' . $value . "<br>";
}

// 変数[]で指定する書き方
$people[0] = '鬼頭';
$people[1] = '森';
$people[2] = '棚橋';

foreach ($people as $key => $value) {
    echo 'キーは' . $key . '、名前は' . $value . '<br>';
}

// キーのない配列
$a = ['A', 'B', 'C'];

var_dump($a);

//キーを省略した場合
$b[] = 'C';
$b[] = 'D';
$b[] = 'E';

var_dump($b);

// arrayを使った配列の書き方
// 近年では、[]を使った配列の作成が一般的
$c = array('E', 'F', 'G');
