<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET NEWS ID
// ==========================================

$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($news_id <= 0) {
    header("Location: news.php");
    exit;
}


// ==========================================
// GET NEWS
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
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

$result = mysqli_stmt_get_result($stmt);

$news = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$news) {
    header("Location: news.php");
    exit;
}


$error = '';


// ==========================================
// CREATE SLUG
// ==========================================

function createNewsSlug($text)
{
    $text = trim($text);

    $text = mb_strtolower($text, 'UTF-8');

    $text = strtr($text, [
        'ë' => 'e',
        'ç' => 'c'
    ]);

    $text = preg_replace(
        '/[^a-z0-9]+/u',
        '-',
        $text
    );

    return trim($text, '-');
}


// ==========================================
// UPDATE NEWS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $published_at = trim($_POST['published_at'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;


    if ($title === '') {

        $error = "Titulli është i detyrueshëm.";

    } elseif ($content === '') {

        $error = "Përmbajtja është e detyrueshme.";

    }


    // ======================================
    // SLUG
    // ======================================

    if ($error === '') {

        $slug = createNewsSlug($title);

        if ($slug === '') {
            $slug = 'lajm-' . $news_id;
        }


        $base_slug = $slug;
        $counter = 1;


        while (true) {

            $check = mysqli_prepare(
                $conn,
                "SELECT id
                 FROM news
                 WHERE slug = ?
                 AND id != ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $check,
                "si",
                $slug,
                $news_id
            );

            mysqli_stmt_execute($check);

            $check_result =
                mysqli_stmt_get_result($check);

            $exists =
                mysqli_num_rows($check_result) > 0;

            mysqli_stmt_close($check);


            if (!$exists) {
                break;
            }


            $counter++;

            $slug =
                $base_slug .
                '-' .
                $counter;
        }

    }


    // ======================================
    // DATE
    // ======================================

    if (
        $error === '' &&
        $published_at === ''
    ) {

        $published_at =
            $news['published_at'];

    }


    // ======================================
    // IMAGE
    // ======================================

    $image_name =
        $news['image'];


    if (
        $error === '' &&
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['image']['error'] !== UPLOAD_ERR_OK
        ) {

            $error =
                "Gabim gjatë ngarkimit të fotografisë.";

        } elseif (
            $_FILES['image']['size'] > 8 * 1024 * 1024
        ) {

            $error =
                "Fotografia nuk mund të jetë më e madhe se 8MB.";

        } else {

            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif'
            ];


            $finfo =
                finfo_open(FILEINFO_MIME_TYPE);

            $mime_type =
                finfo_file(
                    $finfo,
                    $_FILES['image']['tmp_name']
                );

            finfo_close($finfo);


            if (
                !in_array(
                    $mime_type,
                    $allowed_types,
                    true
                )
            ) {

                $error =
                    "Lejohen vetëm JPG, PNG, WEBP dhe GIF.";

            } else {

                $extensions = [

                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif'

                ];


                $extension =
                    $extensions[$mime_type];


                $new_image_name =
                    'news_' .
                    time() .
                    '_' .
                    bin2hex(random_bytes(5)) .
                    '.' .
                    $extension;


                $upload_dir =
                    '../assets/uploads/news/';


                if (!is_dir($upload_dir)) {

                    mkdir(
                        $upload_dir,
                        0755,
                        true
                    );

                }


                $upload_path =
                    $upload_dir .
                    $new_image_name;


                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $upload_path
                    )
                ) {

                    // Fshi fotografinë e vjetër
                    if (
                        !empty($news['image'])
                    ) {

                        $old_image =
                            $upload_dir .
                            $news['image'];

                        if (
                            file_exists($old_image)
                        ) {

                            unlink($old_image);

                        }

                    }


                    $image_name =
                        $new_image_name;

                } else {

                    $error =
                        "Fotografia nuk mund të ruhej.";

                }

            }

        }

    }


    // ======================================
    // UPDATE DATABASE
    // ======================================

    if ($error === '') {

        $update = mysqli_prepare(
            $conn,
            "UPDATE news
             SET
                title = ?,
                slug = ?,
                image = ?,
                excerpt = ?,
                category = ?,
                published_at = ?,
                featured = ?,
                content = ?
             WHERE id = ?"
        );


        mysqli_stmt_bind_param(
            $update,
            "ssssssisi",
            $title,
            $slug,
            $image_name,
            $excerpt,
            $category,
            $published_at,
            $featured,
            $content,
            $news_id
        );


        if (
            mysqli_stmt_execute($update)
        ) {

            mysqli_stmt_close($update);

            header(
                "Location: news.php?updated=1"
            );

            exit;

        } else {

            $error =
                "Ndodhi një gabim gjatë përditësimit.";

            mysqli_stmt_close($update);

        }

    }


    // Përditëso të dhënat në ekran
    $news['title'] = $title;
    $news['slug'] = $slug ?? $news['slug'];
    $news['image'] = $image_name;
    $news['excerpt'] = $excerpt;
    $news['category'] = $category;
    $news['published_at'] = $published_at;
    $news['featured'] = $featured;
    $news['content'] = $content;

}

?>

<!DOCTYPE html>
<html lang="sq">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Ndrysho lajm | Jakup Ceraja Admin
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/animations.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


<div class="admin-layout">


    <!-- SIDEBAR -->

    <aside class="admin-sidebar">

        <div class="admin-logo">

            <div class="admin-logo-icon">

                <i class="fa-solid fa-book-open"></i>

            </div>

            <div>

                <strong>
                    Jakup Ceraja
                </strong>

                <span>
                    Admin Panel
                </span>

            </div>

        </div>


        <nav class="admin-nav">

            <a href="dashboard.php">

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>


            <a href="books.php">

                <i class="fa-solid fa-book"></i>

                <span>Libra</span>

            </a>


            <a href="authors.php">

                <i class="fa-solid fa-pen-nib"></i>

                <span>Autorë</span>

            </a>


            <a href="categories.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>Kategori</span>

            </a>


            <a
                href="news.php"
                class="active"
            >

                <i class="fa-solid fa-newspaper"></i>

                <span>Lajme</span>

            </a>


            <a href="gallery.php">

                <i class="fa-solid fa-images"></i>

                <span>Galeri</span>

            </a>


            <a href="messages.php">

                <i class="fa-solid fa-envelope"></i>

                <span>Mesazhe</span>

            </a>


            <a href="orders.php">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>Porosi</span>

            </a>


            <a href="settings.php">

                <i class="fa-solid fa-gear"></i>

                <span>Cilësime</span>

            </a>

        </nav>


        <div class="admin-sidebar-bottom">

            <a
                href="../index.php"
                target="_blank"
            >

                <i class="fa-solid fa-globe"></i>

                Shiko faqen

            </a>


            <a href="logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                Dil

            </a>

        </div>

    </aside>



    <!-- MAIN -->

    <main class="admin-main">


        <header class="admin-topbar">

            <button
                type="button"
                class="admin-menu-toggle"
                id="adminMenuToggle"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div class="admin-topbar-title">

                <span>ADMIN PANEL</span>

                <h1>
                    Ndrysho lajm
                </h1>

            </div>


            <div class="admin-topbar-actions">

                <button
                    type="button"
                    class="admin-icon-btn"
                    id="darkModeToggle"
                    title="Dark mode"
                >

                    <i class="fa-solid fa-moon"></i>

                </button>

            </div>

        </header>



        <div class="admin-content">


            <div class="news-form-page">


                <a
                    href="news.php"
                    class="news-back-button"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te lajmet

                </a>



                <section class="admin-panel-card news-form-card">


                    <div class="news-form-header">

                        <div class="news-form-icon">

                            <i class="fa-solid fa-pen"></i>

                        </div>


                        <div>

                            <span>
                                LAJME
                            </span>

                            <h2>
                                Ndrysho lajmin
                            </h2>

                            <p>
                                Përditëso të dhënat e këtij lajmi.
                            </p>

                        </div>

                    </div>



                    <?php if ($error !== ''): ?>

                        <div class="news-form-message error">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </div>

                    <?php endif; ?>



                    <form
                        method="POST"
                        enctype="multipart/form-data"
                        class="news-form"
                    >


                        <div class="news-form-field">

                            <label for="title">
                                Titulli *
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?php
                                    echo htmlspecialchars(
                                        $news['title']
                                    );
                                ?>"
                                required
                            >

                        </div>



                        <div class="news-form-grid">


                            <div class="news-form-field">

                                <label for="category">
                                    Kategoria
                                </label>

                                <input
                                    type="text"
                                    id="category"
                                    name="category"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $news['category'] ?? ''
                                        );
                                    ?>"
                                >

                            </div>


                            <div class="news-form-field">

                                <label for="published_at">
                                    Data e publikimit
                                </label>

                                <input
                                    type="datetime-local"
                                    id="published_at"
                                    name="published_at"
                                    value="<?php
                                        echo !empty(
                                            $news['published_at']
                                        )
                                            ? date(
                                                'Y-m-d\TH:i',
                                                strtotime(
                                                    $news['published_at']
                                                )
                                            )
                                            : '';
                                    ?>"
                                >

                            </div>


                        </div>



                        <div class="news-form-field">

                            <label for="excerpt">
                                Përshkrim i shkurtër
                            </label>

                            <textarea
                                id="excerpt"
                                name="excerpt"
                                rows="4"
                            ><?php
                                echo htmlspecialchars(
                                    $news['excerpt'] ?? ''
                                );
                            ?></textarea>

                        </div>



                        <div class="news-form-field">

                            <label for="content">
                                Përmbajtja *
                            </label>

                            <textarea
                                id="content"
                                name="content"
                                rows="12"
                                required
                            ><?php
                                echo htmlspecialchars(
                                    $news['content'] ?? ''
                                );
                            ?></textarea>

                        </div>



                        <!-- CURRENT IMAGE -->

                        <?php if (!empty($news['image'])): ?>

                            <div class="news-current-image">

                                <label>
                                    Fotografia aktuale
                                </label>

                                <img
                                    src="../assets/uploads/news/<?php
                                        echo htmlspecialchars(
                                            $news['image']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $news['title']
                                        );
                                    ?>"
                                >

                            </div>

                        <?php endif; ?>



                        <!-- NEW IMAGE -->

                        <div class="news-form-field">

                            <label for="image">
                                Ndrysho fotografinë
                            </label>

                            <div class="news-upload-box">

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    accept="image/jpeg,image/png,image/webp,image/gif"
                                >

                                <div
                                    class="news-upload-content"
                                    id="newsUploadContent"
                                >

                                    <div class="news-upload-icon">

                                        <i class="fa-solid fa-cloud-arrow-up"></i>

                                    </div>

                                    <strong>
                                        Zgjidh fotografi të re
                                    </strong>

                                    <span>
                                        JPG, PNG, WEBP ose GIF
                                    </span>

                                    <small>
                                        Lëre bosh nëse nuk dëshiron ta ndryshosh.
                                    </small>

                                </div>

                                <img
                                    src=""
                                    alt="Preview"
                                    id="newsPreview"
                                    class="news-preview"
                                >

                            </div>

                        </div>



                        <div class="news-featured-option">

                            <label>

                                <input
                                    type="checkbox"
                                    name="featured"
                                    value="1"
                                    <?php
                                    echo (int)$news['featured'] === 1
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span>

                                    <i class="fa-solid fa-star"></i>

                                    Shfaqe si lajm të veçuar

                                </span>

                            </label>

                        </div>



                        <div class="news-form-actions">

                            <a
                                href="news.php"
                                class="news-cancel-button"
                            >

                                Anulo

                            </a>


                            <button
                                type="submit"
                                class="admin-primary-button"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Ruaj ndryshimet

                            </button>

                        </div>


                    </form>


                </section>


            </div>


        </div>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const input =
            document.getElementById('image');

        const preview =
            document.getElementById('newsPreview');

        const content =
            document.getElementById('newsUploadContent');


        if (!input) {
            return;
        }


        input.addEventListener(
            'change',
            function () {

                const file =
                    this.files[0];

                if (!file) {
                    return;
                }

                const reader =
                    new FileReader();

                reader.onload =
                    function (event) {

                        preview.src =
                            event.target.result;

                        preview.style.display =
                            'block';

                        content.style.display =
                            'none';

                    };

                reader.readAsDataURL(file);

            }
        );

    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>