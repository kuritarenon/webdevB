<!-- 問題④そのままで、記述方法を制御構文（代替構文）に変換して答えてください。 -->
<?php
$score = rand(0, 100);

echo "スコア: " . $score . "<br>";

if ($score >= 80):
    echo "優";
elseif ($score >= 60):
    echo "良";
else:
    echo "可";
endif;
