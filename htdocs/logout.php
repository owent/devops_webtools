<?php

session_start();

$_SESSION['user'] = null;

if (isset($_COOKIE['oauth_redirect'])) {
    setcookie('oauth_redirect', null, time() - 1);
}

header("Location: index.php");
