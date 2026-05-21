<?php
echo "<s>test</s><br>";
echo htmlspecialchars("<s>test</s><br>", ENT_QUOTES, 'UTF-8');

//input2.htmlから
echo htmlspecialchars($_POST['a'], ENT_QUOTES, 'UTF-8');
//echo $_POST['a'];
