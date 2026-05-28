<?php
// confirm.php
//XSS対策の関数
function str2html(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

//$_POSTの値は、name属性値
//PHPの「null　合体演算子」です。入っていればその値。なければ””（空白）を返す
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$firstName = $_POST['first_name'] ?? '';
?>

<!-- PHPはここで閉じている -->
<!-- これ以降は、HTML -->
<h1>入力内容の確認</h1>
<ul>
    <li>メールアドレス：<?php echo str2html($email) ?></li>
    <li>ユーザー名：<?php echo str2html($username) ?></li>
    <li>お名前：<?php echo str2html($lastName) ?> <?php echo str2html($firstName) ?></li>
</ul>
<p>こちらでよろしいでしょうか？</p>
<button>はい</button>
<button>いいえ</button>