<?php
// inc/login_check.php
if (!isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
