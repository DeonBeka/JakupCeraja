<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';

$success = '';
$error = '';


// ==========================================
// ADD GALLERY IMAGE
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');


    // ======================================
    // CHECK TITLE
    // ======================================

    if ($title === '') {

        $error = "Ju lutem vendosni një titull.";

    }

    // ======================================
    // CHECK IMAGE
    // ======================================

    elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = "Ju lutem zgjidhni një fotografi.";

    }

    else {

        $image = $_FILES['image'];


        // ==================================
        // FILE SIZE
        // ==================================

        if ($image['size'] > 8 * 1024 * 1024) {

            $error =
                "Fotografia nuk mund të jetë më e madhe se 8MB.";

        }

        else {

            // ==================================
            // CHECK TYPE
            // ==================================

            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif'
            ];


            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $mime_type = finfo_file(
                $finfo,
                $image['tmp_name']
            );

            finfo_close($finfo);


            if (!in_array($mime_type, $allowed_types, true)) {

                $error =
                    "Lejohen vetëm JPG, PNG, WEBP dhe GIF.";

            }

            else {

                // ==================================
                // UPLOAD DIRECTORY
                // ==================================

                $upload_dir =
                    '../assets/uploads/gallery/';


                if (!is_dir($upload_dir)) {

                    mkdir(
                        $upload_dir,
                        0755,
                        true
                    );

                }


                // ==================================
                // EXTENSION
                // ==================================

                $extensions = [

                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif'

                ];


                $extension =
                    $extensions[$mime_type];


                // ==================================
                // UNIQUE FILE NAME
                // ==================================

                $file_name =
                    'gallery_' .
                    time() .
                    '_' .
                    bin2hex(
                        random_bytes(5)
                    ) .
                    '.' .
                    $extension;


                $target =
                    $upload_dir .
                    $file_name;


                // ==================================
                // MOVE IMAGE
                // ==================================

                if (
                    move_uploaded_file(
                        $image['tmp_name'],
                        $target
                    )
                ) {


                    // ==================================
                    // DATABASE
                    // ==================================

                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO gallery
                        (image, title)
                        VALUES (?, ?)"
                    );


                    mysqli_stmt_bind_param(
                        $stmt,
                        "ss",
                        $file_name,
                        $title
                    );


                    if (mysqli_stmt_execute($stmt)) {

                        mysqli_stmt_close($stmt);


                        header(
                            "Location: gallery.php?added=1"
                        );

                        exit;

                    }


                    // Nëse databaza dështon,
                    // fshije foton e sapo-ngarkuar

                    unlink($target);

                    mysqli_stmt_close($stmt);


                    $error =
                        "Fotografia u ngarkua, por nuk u ruajt në databazë.";

                }

                else {

                    $error =
                        "Ndodhi një problem gjatë ngarkimit të fotografisë.";

                }

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
        Shto fotografi | Jakup Ceraja Admin
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


            <a href="news.php">

                <i class="fa-solid fa-newspaper"></i>

                <span>
                    Lajme
                </span>

            </a>


            <a
                href="gallery.php"
                class="active"
            >

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
                    Shto fotografi
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


            <div class="gallery-form-page">


                <!-- BACK -->

                <a
                    href="gallery.php"
                    class="gallery-back-button"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te galeria

                </a>



                <!-- FORM CARD -->

                <section class="admin-panel-card gallery-form-card">


                    <div class="gallery-form-header">

                        <div class="gallery-form-icon">

                            <i class="fa-solid fa-image"></i>

                        </div>

                        <div>

                            <span>
                                GALERI
                            </span>

                            <h2>
                                Shto fotografi të re
                            </h2>

                            <p>
                                Ngarko një fotografi të re në galeri.
                            </p>

                        </div>

                    </div>



                    <!-- ERROR -->

                    <?php if ($error !== ''): ?>

                        <div class="gallery-form-message error">

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
                                placeholder="P.sh. Panairi i Librit"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['title'] ?? ''
                                    );
                                ?>"
                                required
                            >

                        </div>



                        <!-- IMAGE -->

                        <div class="gallery-form-field">

                            <label for="image">
                                Fotografia
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
                                    required
                                >


                                <div
                                    class="gallery-upload-content"
                                    id="galleryUploadContent"
                                >

                                    <div class="gallery-upload-icon">

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
                                    id="galleryPreview"
                                    class="gallery-preview"
                                    alt="Preview"
                                >

                            </div>

                        </div>



                        <!-- BUTTONS -->

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

                                <i class="fa-solid fa-plus"></i>

                                Shto fotografinë

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

                const file =
                    this.files[0];


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