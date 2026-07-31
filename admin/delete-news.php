<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


$news_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($news_id <= 0) {

    header("Location: news.php");
    exit;

}


// Merr fotografinë para fshirjes

$stmt = mysqli_prepare(
    $conn,
    "SELECT image
     FROM news
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $news_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$news =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$news) {

    header("Location: news.php");
    exit;

}


// Fshi nga databaza

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM news
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $news_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


// Fshi fotografinë

if (!empty($news['image'])) {

    $image_path =
        '../assets/uploads/news/' .
        $news['image'];

    if (file_exists($image_path)) {

        unlink($image_path);

    }

}


header("Location: news.php?deleted=1");
exit;