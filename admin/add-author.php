<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';

$error = '';

$fullname = '';
$biography = '';


// ==========================================
// UPLOAD DIRECTORY
// ==========================================

$upload_dir = '../assets/uploads/authors/';


// Krijo folderin nëse nuk ekziston
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}


// ==========================================
// FORM SUBMIT
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $biography = trim($_POST['biography'] ?? '');

    $photo_name = '';


    // ======================================
    // VALIDATE NAME
    // ======================================

    if ($fullname === '') {

        $error = "Ju lutem shkruani emrin e autorit.";

    } else {


        // ==================================
        // PHOTO UPLOAD
        // ==================================

        if (
            isset($_FILES['photo']) &&
            $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if (
                $_FILES['photo']['error'] !== UPLOAD_ERR_OK
            ) {

                $error =
                    "Ndodhi një problem gjatë ngarkimit të fotos.";

            } else {

                $file = $_FILES['photo'];


                // Madhësia maksimum 5MB
                if ($file['size'] > 5 * 1024 * 1024) {

                    $error =
                        "Fotoja nuk duhet të jetë më e madhe se 5MB.";

                } else {


                    // ==================================
                    // CHECK FILE TYPE
                    // ==================================

                    $allowed_types = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];


                    $file_type =
                        mime_content_type(
                            $file['tmp_name']
                        );


                    if (
                        !in_array(
                            $file_type,
                            $allowed_types,
                            true
                        )
                    ) {

                        $error =
                            "Lejohen vetëm JPG, PNG dhe WEBP.";

                    } else {


                        // ==================================
                        // EXTENSION
                        // ==================================

                        $extension =
                            strtolower(
                                pathinfo(
                                    $file['name'],
                                    PATHINFO_EXTENSION
                                )
                            );


                        // ==================================
                        // UNIQUE NAME
                        // ==================================

                        $photo_name =
                            'author_' .
                            time() .
                            '_' .
                            bin2hex(
                                random_bytes(5)
                            ) .
                            '.' .
                            $extension;


                        $destination =
                            $upload_dir .
                            $photo_name;


                        // ==================================
                        // MOVE PHOTO
                        // ==================================

                        if (
                            !move_uploaded_file(
                                $file['tmp_name'],
                                $destination
                            )
                        ) {

                            $error =
                                "Fotoja nuk mund të ruhej.";

                        }

                    }

                }

            }

        }


        // ==================================
        // INSERT INTO DATABASE
        // ==================================

        if ($error === '') {

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO authors
                (
                    fullname,
                    photo,
                    biography
                )
                VALUES (?, ?, ?)"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $fullname,
                $photo_name,
                $biography
            );


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header(
                    "Location: authors.php?success=added"
                );

                exit;

            } else {

                $error =
                    "Ndodhi një gabim gjatë ruajtjes së autorit.";

                mysqli_stmt_close($stmt);


                // Fshi foton nëse DB nuk e ruajti autorin
                if (
                    $photo_name !== '' &&
                    file_exists(
                        $upload_dir . $photo_name
                    )
                ) {

                    unlink(
                        $upload_dir . $photo_name
                    );

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
        Shto Autor | Jakup Ceraja Admin
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


            <a
                href="authors.php"
                class="active"
            >

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
                    Shto Autor
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


                <a
                    href="authors.php"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te autorët

                </a>

            </div>


        </header>



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <div class="author-form-page">


                <div class="admin-welcome">

                    <div>

                        <span>
                            MENAXHIMI I AUTORËVE
                        </span>

                        <h2>
                            Shto autor të ri
                        </h2>

                        <p>
                            Plotësoni të dhënat e autorit
                            dhe ngarkoni fotografinë.
                        </p>

                    </div>


                    <div class="admin-welcome-icon">

                        <i class="fa-solid fa-user-plus"></i>

                    </div>

                </div>



                <!-- ERROR -->

                <?php if ($error !== ''): ?>

                    <div class="author-form-message error">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </span>

                    </div>

                <?php endif; ?>



                <!-- FORM -->

                <section class="admin-panel-card author-form-card">


                    <div class="admin-panel-header">

                        <div>

                            <span>
                                INFORMACIONET
                            </span>

                            <h3>
                                Të dhënat e autorit
                            </h3>

                        </div>

                    </div>



                    <form
                        method="POST"
                        enctype="multipart/form-data"
                        class="author-form"
                    >


                        <!-- FULLNAME -->

                        <div class="author-form-field">

                            <label for="fullname">

                                Emri i plotë
                                <span>*</span>

                            </label>


                            <input
                                type="text"
                                id="fullname"
                                name="fullname"
                                value="<?php
                                    echo htmlspecialchars($fullname);
                                ?>"
                                placeholder="p.sh. Ismail Kadare"
                                required
                            >

                        </div>



                        <!-- PHOTO -->

                        <div class="author-form-field">

                            <label for="photo">
                                Fotografia
                            </label>


                            <div class="author-upload-box">


                                <div class="author-upload-icon">

                                    <i class="fa-solid fa-cloud-arrow-up"></i>

                                </div>


                                <div>

                                    <strong>
                                        Zgjidh fotografinë
                                    </strong>

                                    <span>
                                        JPG, PNG ose WEBP — maksimumi 5MB
                                    </span>

                                </div>


                                <input
                                    type="file"
                                    id="photo"
                                    name="photo"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                >

                            </div>


                            <div
                                id="photoPreview"
                                class="author-photo-preview"
                            ></div>

                        </div>



                        <!-- BIOGRAPHY -->

                        <div class="author-form-field">

                            <label for="biography">
                                Biografia
                            </label>


                            <textarea
                                id="biography"
                                name="biography"
                                rows="8"
                                placeholder="Shkruani biografinë e autorit..."
                            ><?php
                                echo htmlspecialchars($biography);
                            ?></textarea>

                        </div>



                        <!-- BUTTONS -->

                        <div class="author-form-actions">


                            <a
                                href="authors.php"
                                class="admin-secondary-button"
                            >

                                <i class="fa-solid fa-xmark"></i>

                                Anulo

                            </a>


                            <button
                                type="submit"
                                class="admin-primary-button"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Ruaj autorin

                            </button>


                        </div>


                    </form>


                </section>


            </div>


        </div>


    </main>


</div>



<!-- ======================================
     PHOTO PREVIEW
======================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const photoInput =
            document.getElementById('photo');

        const preview =
            document.getElementById('photoPreview');


        photoInput.addEventListener(
            'change',
            function () {

                preview.innerHTML = '';


                if (
                    !photoInput.files ||
                    !photoInput.files[0]
                ) {
                    return;
                }


                const file =
                    photoInput.files[0];


                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        preview.innerHTML = `

                            <div class="author-preview-image-wrapper">

                                <img
                                    src="${event.target.result}"
                                    class="author-preview-image"
                                    alt="Preview"
                                >

                                <span>
                                    Parashikim i fotos
                                </span>

                            </div>

                        `;

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