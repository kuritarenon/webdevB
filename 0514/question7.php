<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認問題</title>
</head>

<body>
    <p>①次のif-elseif-else構文を代替構文にしてください。</p>
    <?php
    $num = 2;
    if ($num === 1) {
        echo "一です";
    } elseif ($num === 2) {
        echo "二です";
    } else {
        echo "それ以外です";
    }
    ?>
    <!-- -------------------------------------------- -->
    <?php $num = 2; ?>
    <?php if ($num === 1): ?>
        <p>一です</p>
    <?php elseif ($num === 2): ?>
        <p>二です</p>
    <?php else: ?>
        <p>それ以外です</p>
    <?php endif; ?>



    <p>②以下のコードを代替構文に書き換えてください。</p>
    <?php
    $isMember = true;
    if ($isMember) {
        echo "<p>会員様向けの情報です。</p>";
    }
    ?>
    <!-- -------------------------------------------- -->
    <?php $isMember = true; ?>
    <?php if ($isMember) : ?>
        <p>会員様向けの情報です。</p>
    <?php endif; ?>



    <p>③HTMLのul要素の中で、for文を使ってli要素を5個出力するコードを代替構文で書いてください。<br>
        例：
    <ul>
        <li>1</li>
        <li>2</li>
        <li>3</li>
        <li>4</li>
        <li>5</li>
    </ul>
    </p>
    <!-- -------------------------------------------- -->
    <ul>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <li><?php echo $i; ?></li>
        <?php endfor; ?>
    </ul>



    <p>④以下のfor文を代替構文に書き換えてください。</p>
    <?php
    for ($i = 1; $i <= 3; $i++) {
        echo "<p>番号: $i</p>";
    }
    ?>
    <!-- -------------------------------------------- -->
    <?php for ($i = 1; $i <= 3; $i++) : ?>
        <p>番号: <?php echo $i; ?></p>
    <?php endfor; ?>



    <p>⑤以下のコードを代替構文に書き換えてください。</p>
    <?php
    $users = ['太郎', '花子', '次郎'];
    foreach ($users as $user) {
        echo "<p>{$user}さん、ようこそ！</p>";
    }
    ?>
    <!-- -------------------------------------------- -->
    <?php foreach ($users as $user): ?>
        <p><?php echo $user; ?>さん、ようこそ！</p>
    <?php endforeach; ?>



</body>

</html>