<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';

$error = '';
$success = '';


// ==========================================
// GET AUTHORS
// ==========================================

$authors = [];

$authors_query = mysqli_query(
    $conn,
    "SELECT id, fullname
     FROM authors
     ORDER BY fullname ASC"
);

if ($authors_query) {

    while ($author = mysqli_fetch_assoc($authors_query)) {
        $authors[] = $author;
    }

}


// ==========================================
// GET CATEGORIES
// ==========================================

$categories = [];

$categories_query = mysqli_query(
    $conn,
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

if ($categories_query) {

    while ($category = mysqli_fetch_assoc($categories_query)) {
        $categories[] = $category;
    }

}


// ==========================================
// ADD BOOK
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $author_id = (int)($_POST['author_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $publish_date = trim($_POST['publish_date'] ?? '');
    $price = isset($_POST['price'])
    ? (float)$_POST['price']
    : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;


    // ======================================
    // VALIDATION
    // ======================================

    if ($title === '') {

        $error = "Ju lutem shkruani titullin e librit.";

    } elseif ($author_id <= 0) {

        $error = "Ju lutem zgjidhni autorin.";

    } elseif ($category_id <= 0) {

        $error = "Ju lutem zgjidhni kategorinë.";
    
    } elseif ($price <= 0) {

        $error = "Ju lutem zgjidhni cmimin.";

    } else {


        // ==================================
        // COVER
        // ==================================

        $cover_path = 'assets/images/books/default-book.jpg';


        if (
            isset($_FILES['cover']) &&
            $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE
        ) {


            if (
                $_FILES['cover']['error'] !== UPLOAD_ERR_OK
            ) {

                $error =
                    "Ndodhi një problem gjatë upload-it të cover-it.";

            } else {


                $allowed_types = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                $file_type =
                    mime_content_type(
                        $_FILES['cover']['tmp_name']
                    );


                if (
                    !in_array(
                        $file_type,
                        $allowed_types,
                        true
                    )
                ) {

                    $error =
                        "Cover-i duhet të jetë JPG, PNG ose WEBP.";

                } elseif (
                    $_FILES['cover']['size'] > 5 * 1024 * 1024
                ) {

                    $error =
                        "Cover-i nuk duhet të jetë më i madh se 5MB.";

                } else {


                    $upload_directory =
                        '../assets/images/books/';


                    if (
                        !is_dir($upload_directory)
                    ) {

                        mkdir(
                            $upload_directory,
                            0755,
                            true
                        );

                    }


                    $extension =
                        strtolower(
                            pathinfo(
                                $_FILES['cover']['name'],
                                PATHINFO_EXTENSION
                            )
                        );


                    $filename =
                        'book_' .
                        time() .
                        '_' .
                        bin2hex(
                            random_bytes(4)
                        ) .
                        '.' .
                        $extension;


                    $target =
                        $upload_directory .
                        $filename;


                    if (
                        move_uploaded_file(
                            $_FILES['cover']['tmp_name'],
                            $target
                        )
                    ) {

                        $cover_path =
                            'assets/images/books/' .
                            $filename;

                    } else {

                        $error =
                            "Cover-i nuk mund të ruhej.";

                    }

                }

            }

        }


        // ==================================
        // INSERT BOOK
        // ==================================

        if ($error === '') {


            if ($publish_date === '') {

                $publish_date = null;

            }


            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO books
                (
                    title,
                    author_id,
                    category_id,
                    cover,
                    description,
                    publish_date,
                    price,
                    featured
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "siisssi",
                $title,
                $author_id,
                $category_id,
                $cover_path,
                $description,
                $publish_date,
                $price,
                $featured
            );


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header(
                    "Location: books.php?added=1"
                );

                exit;

            } else {

                $error =
                    "Libri nuk mund të ruhej: " .
                    mysqli_error($conn);

                mysqli_stmt_close($stmt);

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
        Shto libër | Jakup Ceraja
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


            <a
                href="books.php"
                class="active"
            >

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
                    MENAXHIMI I LIBRAVE
                </span>

                <h1>
                    Shto libër
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
                    href="books.php"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te librat

                </a>


            </div>


        </header>



        <!-- CONTENT -->

        <div class="admin-content">


            <!-- INTRO -->

            <div class="admin-welcome">

                <div>

                    <span>
                        LIBËR I RI
                    </span>

                    <h2>
                        Shto një libër
                    </h2>

                    <p>
                        Plotëso të dhënat e librit
                        për ta publikuar në website.
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-plus"></i>

                </div>

            </div>



            <!-- ERROR -->

            <?php if ($error !== ''): ?>

                <div class="add-book-alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </span>

                </div>

            <?php endif; ?>



            <!-- FORM -->

            <section class="admin-panel-card add-book-card">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            INFORMACIONI
                        </span>

                        <h3>
                            Të dhënat e librit
                        </h3>

                    </div>


                    <div class="add-book-header-icon">

                        <i class="fa-solid fa-book"></i>

                    </div>

                </div>



                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="add-book-form"
                >


                    <!-- ==================================
                         TITLE
                    =================================== -->

                    <div class="add-book-field full-width">

                        <label for="title">
                            Titulli i librit
                        </label>

                        <div class="add-book-input">

                            <i class="fa-solid fa-heading"></i>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                placeholder="p.sh. Gjurmët e kohës"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['title'] ?? ''
                                    );
                                ?>"
                                required
                            >

                        </div>

                    </div>



                    <!-- ==================================
                         AUTHOR
                    =================================== -->

                    <div class="add-book-field">

                        <label for="author_id">
                            Autori
                        </label>

                        <div class="add-book-input">

                            <i class="fa-solid fa-pen-nib"></i>

                            <select
                                id="author_id"
                                name="author_id"
                                required
                            >

                                <option value="">
                                    Zgjidh autorin
                                </option>


                                <?php foreach (
                                    $authors
                                    as $author
                                ): ?>

                                    <option
                                        value="<?php
                                            echo (int)$author['id'];
                                        ?>"
                                        <?php
                                        echo (
                                            (int)(
                                                $_POST['author_id']
                                                ?? 0
                                            )
                                            ===
                                            (int)$author['id']
                                        )
                                        ? 'selected'
                                        : '';
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $author['fullname']
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>

                    </div>



                    <!-- ==================================
                         CATEGORY
                    =================================== -->

                    <div class="add-book-field">

                        <label for="category_id">
                            Kategoria
                        </label>

                        <div class="add-book-input">

                            <i class="fa-solid fa-layer-group"></i>

                            <select
                                id="category_id"
                                name="category_id"
                                required
                            >

                                <option value="">
                                    Zgjidh kategorinë
                                </option>


                                <?php foreach (
                                    $categories
                                    as $category
                                ): ?>

                                    <option
                                        value="<?php
                                            echo (int)$category['id'];
                                        ?>"
                                        <?php
                                        echo (
                                            (int)(
                                                $_POST['category_id']
                                                ?? 0
                                            )
                                            ===
                                            (int)$category['id']
                                        )
                                        ? 'selected'
                                        : '';
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $category['name']
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>

                    </div>



                    <!-- ==================================
                         PUBLISH DATE
                    =================================== -->

                    <div class="add-book-field">

                        <label for="publish_date">
                            Data e publikimit
                        </label>

                        <div class="add-book-input">

                            <i class="fa-solid fa-calendar"></i>

                            <input
                                type="date"
                                id="publish_date"
                                name="publish_date"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['publish_date']
                                        ?? ''
                                    );
                                ?>"
                            >

                        </div>

                    </div>



                    <!-- ==================================
                         COVER
                    =================================== -->

                    <div class="add-book-field">

                        <label for="cover">
                            Cover-i
                        </label>

                        <div class="add-book-file">

                            <input
                                type="file"
                                id="cover"
                                name="cover"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                            <span>
                                Zgjidh cover-in
                            </span>

                            <small>
                                JPG, PNG ose WEBP — max 5MB
                            </small>

                        </div>

                    </div>



                    <!-- ==================================
                         DESCRIPTION
                    =================================== -->

                    <div class="add-book-field full-width">

                        <label for="description">
                            Përshkrimi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="7"
                            placeholder="Shkruaj përshkrimin e librit..."
                        ><?php
                            echo htmlspecialchars(
                                $_POST['description'] ?? ''
                            );
                        ?></textarea>

                    </div>

                    <!-- ==================================
                         Book price
                    =================================== -->
                    <div class="admin-form-group">

                        <label for="price">
                            Çmimi i librit (€)
                        </label>

                        <div class="price-input-wrapper">

                            <input
                                type="number"
                                id="price"
                                name="price"
                                min="0"
                                step="0.01"
                                placeholder="p.sh. 10.00"
                                required
                            >

                            <span>€</span>

                        </div>

                    </div>


                    <!-- ==================================
                         FEATURED
                    =================================== -->

                    <div class="add-book-featured">

                        <label class="featured-checkbox">

                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                <?php
                                echo isset(
                                    $_POST['featured']
                                )
                                ? 'checked'
                                : '';
                                ?>
                            >

                            <span class="featured-checkmark">

                                <i class="fa-solid fa-check"></i>

                            </span>


                            <span class="featured-text">

                                <strong>
                                    Libër i veçuar
                                </strong>

                                <small>
                                    Shfaqe librin si të veçuar
                                    në website.
                                </small>

                            </span>

                        </label>

                    </div>



                    <!-- ==================================
                         ACTIONS
                    =================================== -->

                    <div class="add-book-actions">


                        <a
                            href="books.php"
                            class="add-book-cancel"
                        >

                            <i class="fa-solid fa-xmark"></i>

                            Anulo

                        </a>


                        <button
                            type="submit"
                            class="admin-primary-button"
                        >

                            <i class="fa-solid fa-plus"></i>

                            Shto librin

                        </button>


                    </div>


                </form>


            </section>


        </div>


    </main>


</div>



<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>