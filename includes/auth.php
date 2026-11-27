<?php

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../includes/database.php';
    jc_session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit;
}