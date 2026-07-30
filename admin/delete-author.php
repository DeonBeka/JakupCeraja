<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET AUTHOR ID
// ==========================================

$author_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($author_id <= 0) {

    header("Location: authors.php");
    exit;

}


// ==========================================
// GET AUTHOR
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, photo
     FROM authors
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $author_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$author = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$author) {

    header("Location: authors.php");
    exit;

}


// ==========================================
// CHECK IF BOOKS USE THIS AUTHOR
// ==========================================

$check = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM books
     WHERE author_id = ?"
);

mysqli_stmt_bind_param(
    $check,
    "i",
    $author_id
);

mysqli_stmt_execute($check);

$check_result = mysqli_stmt_get_result($check);

$usage = mysqli_fetch_assoc($check_result);

mysqli_stmt_close($check);


if ((int)$usage['total'] > 0) {

    header(
        "Location: authors.php?error=author_has_books"
    );

    exit;

}


// ==========================================
// DELETE AUTHOR
// ==========================================

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM authors
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $delete,
    "i",
    $author_id
);


if (mysqli_stmt_execute($delete)) {

    mysqli_stmt_close($delete);


    // ======================================
    // DELETE PHOTO
    // ======================================

    if (!empty($author['photo'])) {

        $photo_path =
            '../assets/uploads/authors/' .
            $author['photo'];


        if (
            file_exists($photo_path) &&
            is_file($photo_path)
        ) {

            unlink($photo_path);

        }

    }


    header(
        "Location: authors.php?success=deleted"
    );

    exit;

}


mysqli_stmt_close($delete);


header(
    "Location: authors.php?error=delete_failed"
);

exit;