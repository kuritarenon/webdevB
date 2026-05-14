<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML+PHP4</title>
</head>

<body>
    <?php $count = 1; ?>
    <?php if ($count === 0): ?>
        <p>はじめまして</p>
    <?php else: ?>
        <p>いつもありがとうございます</p>
    <?php endif; ?>
</body>

</html>