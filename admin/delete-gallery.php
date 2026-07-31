<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($id <= 0) {

    header("Location: gallery.php");
    exit;

}


// ==========================================
// GET IMAGE
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT image
     FROM gallery
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$gallery =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$gallery) {

    header("Location: gallery.php");
    exit;

}


// ==========================================
// DELETE DATABASE RECORD
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM gallery
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);


    // ======================================
    // DELETE IMAGE FILE
    // ======================================

    if (!empty($gallery['image'])) {

        $image_path =
            '../assets/uploads/gallery/' .
            $gallery['image'];


        if (
            file_exists($image_path) &&
            is_file($image_path)
        ) {

            unlink($image_path);

        }

    }


    header(
        "Location: gallery.php?deleted=1"
    );

    exit;

}


mysqli_stmt_close($stmt);


header(
    "Location: gallery.php?error=delete"
);

exit;