<?php
// peopleという配列に、配列を代入する
$people[] = ['name' => '佐藤', 'blood' => 'A'];
$people[] = ['name' => '田中', 'blood' => 'B'];
$people[] = ['name' => '加藤', 'blood' => 'O'];

// var_dump($people);

//echo $people[0]['blood'] . '<br>';
//echo $people[2]['name'];

foreach ($people as $people_key => $person) {
    echo '順番は' . $people_key . '<br>';
    foreach ($person as $person_key => $value) {
        echo 'キーは' . $people_key . '、値は' . $value . '<br>';
    }
}
