<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';

$error = '';
$success = '';


// ==========================================
// CREATE SLUG
// ==========================================

function createSlug($text)
{
    $text = trim($text);

    $text = mb_strtolower($text, 'UTF-8');

    $replace = [
        'ë' => 'e',
        'ç' => 'c',
        'Ë' => 'e',
        'Ç' => 'c'
    ];

    $text = strtr($text, $replace);

    $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

    $text = trim($text, '-');

    return $text;
}


// ==========================================
// ADD NEWS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $published_at = trim($_POST['published_at'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $content = trim($_POST['content'] ?? '');


    // ======================================
    // VALIDATION
    // ======================================

    if ($title === '') {

        $error = "Ju lutem vendosni titullin e lajmit.";

    } elseif ($content === '') {

        $error = "Ju lutem vendosni përmbajtjen e lajmit.";

    } elseif (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE &&
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = "Ndodhi një gabim gjatë ngarkimit të fotografisë.";

    }


    // ======================================
    // IMAGE
    // ======================================

    $image_name = '';


    if ($error === '' && isset($_FILES['image'])) {

        if (
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['image']['size'] > 8 * 1024 * 1024) {

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


                if (!in_array($mime_type, $allowed_types, true)) {

                    $error =
                        "Lejohen vetëm JPG, PNG, WEBP dhe GIF.";

                } else {

                    $extensions = [

                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp',
                        'image/gif'  => 'gif'

                    ];


                    $extension =
                        $extensions[$mime_type];


                    $image_name =
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
                        $upload_dir . $image_name;


                    if (
                        !move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $upload_path
                        )
                    ) {

                        $error =
                            "Fotografia nuk mund të ruhej.";

                    }

                }

            }

        }

    }


    // ======================================
    // SAVE NEWS
    // ======================================

    if ($error === '') {

        $slug = createSlug($title);


        if ($slug === '') {

            $slug =
                'lajm-' .
                time();

        }


        // Kontrollo slug-un

        $base_slug = $slug;

        $counter = 1;


        while (true) {

            $check = mysqli_prepare(
                $conn,
                "SELECT id
                 FROM news
                 WHERE slug = ?
                 LIMIT 1"
            );


            mysqli_stmt_bind_param(
                $check,
                "s",
                $slug
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


        // Nëse nuk është vendosur datë
        // përdor datën dhe orën aktuale

        if ($published_at === '') {

            $published_at =
                date('Y-m-d H:i:s');

        }


        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO news
            (
                title,
                slug,
                image,
                excerpt,
                category,
                published_at,
                featured,
                content,
                created_at
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ssssssis",
            $title,
            $slug,
            $image_name,
            $excerpt,
            $category,
            $published_at,
            $featured,
            $content
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);


            header(
                "Location: news.php?added=1"
            );

            exit;

        } else {

            mysqli_stmt_close($stmt);


            // Nëse databaza dështon,
            // fshi foton që u upload-ua

            if (
                $image_name !== ''
            ) {

                $uploaded_file =
                    '../assets/uploads/news/' .
                    $image_name;


                if (
                    file_exists($uploaded_file)
                ) {

                    unlink($uploaded_file);

                }

            }


            $error =
                "Ndodhi një gabim gjatë ruajtjes së lajmit.";

        }

    }

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
        Shto lajm | Jakup Ceraja Admin
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


    <!-- ======================================
         SIDEBAR
    ======================================= -->

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

                <span>
                    Dashboard
                </span>

            </a>


            <a href="books.php">

                <i class="fa-solid fa-book"></i>

                <span>
                    Libra
                </span>

            </a>


            <a href="authors.php">

                <i class="fa-solid fa-pen-nib"></i>

                <span>
                    Autorë
                </span>

            </a>


            <a href="categories.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Kategori
                </span>

            </a>


            <a
                href="news.php"
                class="active"
            >

                <i class="fa-solid fa-newspaper"></i>

                <span>
                    Lajme
                </span>

            </a>


            <a href="gallery.php">

                <i class="fa-solid fa-images"></i>

                <span>
                    Galeri
                </span>

            </a>


            <a href="messages.php">

                <i class="fa-solid fa-envelope"></i>

                <span>
                    Mesazhe
                </span>

            </a>


            <a href="orders.php">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Porosi
                </span>

            </a>


            <a href="settings.php">

                <i class="fa-solid fa-gear"></i>

                <span>
                    Cilësime
                </span>

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



    <!-- ======================================
         MAIN
    ======================================= -->

    <main class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">


            <button
                type="button"
                class="admin-menu-toggle"
                id="adminMenuToggle"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div class="admin-topbar-title">

                <span>
                    ADMIN PANEL
                </span>

                <h1>
                    Shto lajm
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



        <!-- CONTENT -->

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


                    <!-- HEADER -->

                    <div class="news-form-header">


                        <div class="news-form-icon">

                            <i class="fa-solid fa-newspaper"></i>

                        </div>


                        <div>

                            <span>
                                LAJME
                            </span>

                            <h2>
                                Shto lajm të ri
                            </h2>

                            <p>
                                Krijo një lajm të ri për faqen.
                            </p>

                        </div>


                    </div>



                    <!-- ERROR -->

                    <?php if ($error !== ''): ?>

                        <div class="news-form-message error">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </div>

                    <?php endif; ?>



                    <!-- FORM -->

                    <form
                        method="POST"
                        enctype="multipart/form-data"
                        class="news-form"
                    >


                        <!-- TITLE -->

                        <div class="news-form-field">

                            <label for="title">
                                Titulli *
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                placeholder="Shkruaj titullin e lajmit..."
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['title'] ?? ''
                                    );
                                ?>"
                                required
                            >

                        </div>



                        <!-- CATEGORY -->

                        <div class="news-form-grid">


                            <div class="news-form-field">

                                <label for="category">
                                    Kategoria
                                </label>

                                <input
                                    type="text"
                                    id="category"
                                    name="category"
                                    placeholder="p.sh. Kulturë"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $_POST['category'] ?? ''
                                        );
                                    ?>"
                                >

                            </div>



                            <!-- PUBLISHED DATE -->

                            <div class="news-form-field">

                                <label for="published_at">
                                    Data e publikimit
                                </label>

                                <input
                                    type="datetime-local"
                                    id="published_at"
                                    name="published_at"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $_POST['published_at'] ?? ''
                                        );
                                    ?>"
                                >

                            </div>


                        </div>



                        <!-- EXCERPT -->

                        <div class="news-form-field">

                            <label for="excerpt">
                                Përshkrim i shkurtër
                            </label>

                            <textarea
                                id="excerpt"
                                name="excerpt"
                                rows="4"
                                placeholder="Shkruaj një përshkrim të shkurtër..."
                            ><?php
                                echo htmlspecialchars(
                                    $_POST['excerpt'] ?? ''
                                );
                            ?></textarea>

                            <small>
                                Ky tekst mund të shfaqet në kartën e lajmit.
                            </small>

                        </div>



                        <!-- CONTENT -->

                        <div class="news-form-field">

                            <label for="content">
                                Përmbajtja *
                            </label>

                            <textarea
                                id="content"
                                name="content"
                                rows="12"
                                placeholder="Shkruaj përmbajtjen e plotë të lajmit..."
                                required
                            ><?php
                                echo htmlspecialchars(
                                    $_POST['content'] ?? ''
                                );
                            ?></textarea>

                        </div>



                        <!-- IMAGE -->

                        <div class="news-form-field">

                            <label for="image">
                                Fotografia
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
                                        Zgjidh fotografinë
                                    </strong>


                                    <span>
                                        JPG, PNG, WEBP ose GIF
                                    </span>


                                    <small>
                                        Maksimumi 8MB
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



                        <!-- FEATURED -->

                        <div class="news-featured-option">


                            <label>

                                <input
                                    type="checkbox"
                                    name="featured"
                                    value="1"
                                    <?php
                                    echo isset($_POST['featured'])
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



                        <!-- BUTTONS -->

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

                                Publiko lajmin

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

                    preview.style.display =
                        'none';

                    content.style.display =
                        'flex';

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