<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML+PHP5</title>
</head>

<body>
    <?php
    $name = [
        '1' => '佐藤',
        '2' => '鈴木',
        '3' => '高橋',
    ];
    foreach ($name as $key => $value): ?>
        <p>
            <?php echo $key; ?>人目は <?php echo $value; ?>さんです。
        </p>
    <?php endforeach; ?>
</body>

</html>