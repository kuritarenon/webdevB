<?php
session_start();
$_SESSION['a']++; //インクリメント
echo $_SESSION['a'];
