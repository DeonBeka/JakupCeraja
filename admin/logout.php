<?php

require_once __DIR__ . '/../includes/database.php';
jc_session_start();

$_SESSION = [];

session_destroy();

header("Location: login.php");

exit;