<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET CATEGORY ID
// ==========================================

$category_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($category_id <= 0) {

    header("Location: categories.php");
    exit;

}


// ==========================================
// CHECK CATEGORY
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM categories
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $category_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$category = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$category) {

    header("Location: categories.php");
    exit;

}


// ==========================================
// CHECK BOOKS
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM books
     WHERE category_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $category_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if ((int)$data['total'] > 0) {

    header(
        "Location: categories.php?error=has_books"
    );

    exit;

}


// ==========================================
// DELETE
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM categories
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $category_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


header(
    "Location: categories.php?success=deleted"
);

exit;