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
    "SELECT id, image, title
     FROM gallery
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$gallery = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$gallery) {
    header("Location: gallery.php");
    exit;
}


$error = '';


// ==========================================
// UPDATE
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');

    if ($title === '') {

        $error = "Ju lutem vendosni një titull.";

    } else {

        $new_image = $gallery['image'];
        $uploaded_new_image = false;
        $new_image_path = '';


        // ==================================
        // NEW IMAGE
        // ==================================

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

                $error = "Gabim gjatë ngarkimit të fotografisë.";

            } elseif ($_FILES['image']['size'] > 8 * 1024 * 1024) {

                $error = "Fotografia nuk mund të jetë më e madhe se 8MB.";

            } else {

                $allowed_types = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/gif'
                ];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);

                $mime_type = finfo_file(
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

                    $extension = $extensions[$mime_type];


                    $new_image =
                        'gallery_' .
                        time() .
                        '_' .
                        bin2hex(random_bytes(5)) .
                        '.' .
                        $extension;


                    $upload_dir =
                        '../assets/uploads/gallery/';


                    if (!is_dir($upload_dir)) {

                        mkdir(
                            $upload_dir,
                            0755,
                            true
                        );

                    }


                    $new_image_path =
                        $upload_dir . $new_image;


                    if (
                        move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $new_image_path
                        )
                    ) {

                        $uploaded_new_image = true;

                    } else {

                        $error =
                            "Fotografia nuk mund të ruhej.";

                    }

                }

            }

        }


        // ==================================
        // UPDATE DATABASE
        // ==================================

        if ($error === '') {

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE gallery
                 SET title = ?, image = ?
                 WHERE id = ?"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $title,
                $new_image,
                $id
            );


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);


                // Fshi foton e vjetër vetëm
                // pasi update u krye me sukses

                if (
                    $uploaded_new_image &&
                    !empty($gallery['image'])
                ) {

                    $old_path =
                        '../assets/uploads/gallery/' .
                        $gallery['image'];


                    if (
                        file_exists($old_path) &&
                        is_file($old_path)
                    ) {

                        unlink($old_path);

                    }

                }


                header(
                    "Location: gallery.php?updated=1"
                );

                exit;

            } else {

                mysqli_stmt_close($stmt);


                // Nëse DB dështon, fshi foton e re

                if (
                    $uploaded_new_image &&
                    file_exists($new_image_path)
                ) {

                    unlink($new_image_path);

                }


                $error =
                    "Ndodhi një gabim gjatë ruajtjes.";

            }

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
        Ndrysho fotografi | Jakup Ceraja Admin
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


            <a href="news.php">

                <i class="fa-solid fa-newspaper"></i>

                <span>Lajme</span>

            </a>


            <a
                href="gallery.php"
                class="active"
            >

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

                <span>
                    ADMIN PANEL
                </span>

                <h1>
                    Ndrysho fotografi
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


            <div class="gallery-form-page">


                <a
                    href="gallery.php"
                    class="gallery-back-button"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te galeria

                </a>


                <section class="admin-panel-card gallery-form-card">


                    <div class="gallery-form-header">

                        <div class="gallery-form-icon">

                            <i class="fa-solid fa-pen"></i>

                        </div>

                        <div>

                            <span>
                                GALERI
                            </span>

                            <h2>
                                Ndrysho fotografinë
                            </h2>

                            <p>
                                Ndrysho titullin ose zëvendëso fotografinë.
                            </p>

                        </div>

                    </div>



                    <?php if ($error !== ''): ?>

                        <div class="gallery-form-message error">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </div>

                    <?php endif; ?>



                    <form
                        method="POST"
                        enctype="multipart/form-data"
                        class="gallery-form"
                    >


                        <!-- TITLE -->

                        <div class="gallery-form-field">

                            <label for="title">
                                Titulli
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?php
                                    echo htmlspecialchars(
                                        $gallery['title']
                                    );
                                ?>"
                                required
                            >

                        </div>



                        <!-- CURRENT IMAGE -->

                        <div class="gallery-form-field">

                            <label>
                                Fotografia aktuale
                            </label>


                            <div class="gallery-current-image">

                                <img
                                    src="../assets/uploads/gallery/<?php
                                        echo htmlspecialchars(
                                            $gallery['image']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $gallery['title']
                                        );
                                    ?>"
                                >

                            </div>

                        </div>



                        <!-- NEW IMAGE -->

                        <div class="gallery-form-field">

                            <label for="image">
                                Zëvendëso fotografinë
                                <small>
                                    (opsionale)
                                </small>
                            </label>


                            <div
                                class="gallery-upload-box"
                                id="galleryUploadBox"
                            >

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    accept="image/jpeg,image/png,image/webp,image/gif"
                                >


                                <div
                                    class="gallery-upload-content"
                                    id="galleryUploadContent"
                                >

                                    <div class="gallery-upload-icon">

                                        <i class="fa-solid fa-cloud-arrow-up"></i>

                                    </div>

                                    <strong>
                                        Zgjidh fotografinë e re
                                    </strong>

                                    <span>
                                        Lëre bosh nëse nuk dëshiron ta ndryshosh.
                                    </span>

                                    <small>
                                        Maksimumi 8MB
                                    </small>

                                </div>


                                <img
                                    id="galleryPreview"
                                    class="gallery-preview"
                                    alt="Preview"
                                >

                            </div>

                        </div>



                        <div class="gallery-form-actions">


                            <a
                                href="gallery.php"
                                class="gallery-cancel-button"
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
            document.getElementById('galleryPreview');

        const content =
            document.getElementById('galleryUploadContent');


        if (!input) {
            return;
        }


        input.addEventListener(
            'change',
            function () {

                const file = this.files[0];

                if (!file) {

                    preview.style.display = 'none';

                    content.style.display = 'flex';

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