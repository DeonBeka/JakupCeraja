<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET MESSAGE ID
// ==========================================

$message_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


// ==========================================
// CHECK ID
// ==========================================

if ($message_id <= 0) {

    header("Location: messages.php");
    exit;

}


// ==========================================
// DELETE MESSAGE
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM messages
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $message_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


// ==========================================
// RETURN
// ==========================================

header("Location: messages.php");
exit;