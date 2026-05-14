<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML+PHP3</title>
</head>

<body>
    <?php
    $count = 1;
    if ($count === 0) {
        echo "<p>はじめまして</p>" . PHP_EOL;
    } else {
        echo "<p>いつもありがとうございます</p>" . PHP_EOL;
    }
    ?>
</body>

</html>