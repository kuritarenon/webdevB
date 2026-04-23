<?php
//1. 変数 $num = 10 が偶数なら「偶数です」と表示するコードを書いてください。
$num = 10;
if ($num % 2 === 0) {
    echo "偶数です";
}
//2. 変数 $age = 20 が 20 以上かつ 30 未満なら「20代です」と表示するコードを書いてください。
$age = 20;
if ($age >= 20) {
    echo "20代です";
}
if ($age < 30) {
    echo "20代です";
}
/*3. 変数 $x = 10 に対して、以下の条件分岐を作成してください。
    • $x が 10 より小さい →「10より小さい」
    • $x が 10 →「ちょうど10」
    • $x が 10 より大きい →「10より大きい」*/
$x = 10;
if ($x < 10) {
    echo "10より小さい";
} elseif ($x === 10) {
    echo "ちょうど10";
} else {
    echo "10より大きい";
}
//4. 変数 $password = "secret" に対して、入力されたパスワード $input = "secret" が正しければ「ログイン成功」、違えば「パスワードが間違っています」と表示するコードを書いてください。
?>
<form action="question3.php" method="post>
    <input type=" password" name="pass">
    <input type="submit" value="送信">
</form>
<?php
$password = " secret";
$input = "secret";

if ($password === $input) {
    echo "ログイン成功";
} else {
    echo "パスワードが間違っています";
}

//5. input.html と question3.php を連携させて、変数 $loggedIn = "true" の場合に「ようこそ」それ以外に場合には「ログインしてください」と表示するコードを書いてください。
$loggedIn = $_POST["login_status"];
if ($loggedIn = "true") {
    echo "ようこそ";
} else {
    echo "ログインしてください";
}
