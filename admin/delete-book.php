<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET BOOK ID
// ==========================================

$book_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($book_id <= 0) {

    header("Location: books.php");
    exit;

}


// ==========================================
// GET BOOK COVER
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT cover
     FROM books
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $book_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$book = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// ==========================================
// CHECK BOOK
// ==========================================

if (!$book) {

    header("Location: books.php");
    exit;

}


// ==========================================
// DELETE FROM DATABASE
// ==========================================

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM books
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $delete,
    "i",
    $book_id
);


if (mysqli_stmt_execute($delete)) {

    mysqli_stmt_close($delete);


    // ======================================
    // DELETE COVER FILE
    // ======================================

    if (!empty($book['cover'])) {

        /*
         * Fshij vetëm fotot që janë në
         * folderin e librave.
         */

        if (
            strpos(
                $book['cover'],
                'assets/images/books/'
            ) === 0
        ) {

            $cover_file =
                '../' . $book['cover'];


            if (
                file_exists($cover_file)
            ) {

                unlink($cover_file);

            }

        }

    }


    // ======================================
    // SUCCESS
    // ======================================

    header(
        "Location: books.php?deleted=1"
    );

    exit;

}


// ==========================================
// ERROR
// ==========================================

$error =
    mysqli_error($conn);

mysqli_stmt_close($delete);


die(
    "Gabim gjatë fshirjes së librit: " .
    htmlspecialchars($error)
);