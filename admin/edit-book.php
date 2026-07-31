<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ======================================================
// GET BOOK ID
// ======================================================

$book_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($book_id <= 0) {

    header("Location: books.php");
    exit;

}


// ======================================================
// GET BOOK
// ======================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        title,
        price,
        author_id,
        category_id,
        cover,
        description,
        publish_date,
        featured
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


if (!$book) {

    header("Location: books.php");
    exit;

}


// ======================================================
// GET AUTHORS
// ======================================================

$authors_result = mysqli_query(
    $conn,
    "SELECT id, fullname
     FROM authors
     ORDER BY fullname ASC"
);


// ======================================================
// GET CATEGORIES
// ======================================================

$categories_result = mysqli_query(
    $conn,
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


// ======================================================
// VARIABLES
// ======================================================

$success = '';
$error = '';


// ======================================================
// UPDATE BOOK
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $title = trim(
        $_POST['title'] ?? ''
    );


    $price = isset($_POST['price'])
        ? (float)$_POST['price']
        : 0;


    $author_id = isset($_POST['author_id'])
        ? (int)$_POST['author_id']
        : 0;


    $category_id = isset($_POST['category_id'])
        ? (int)$_POST['category_id']
        : 0;


    $description = trim(
        $_POST['description'] ?? ''
    );


    $publish_date = trim(
        $_POST['publish_date'] ?? ''
    );


    $featured = isset($_POST['featured'])
        ? 1
        : 0;


    // ==================================================
    // VALIDATION
    // ==================================================

    if ($title === '') {

        $error =
            "Ju lutem vendosni titullin e librit.";

    } elseif ($price <= 0) {

        $error =
            "Ju lutem vendosni një çmim më të madh se 0€.";

    } elseif ($author_id <= 0) {

        $error =
            "Ju lutem zgjidhni autorin.";

    } elseif ($category_id <= 0) {

        $error =
            "Ju lutem zgjidhni kategorinë.";

    } else {


        // ==============================================
        // CURRENT COVER
        // ==============================================

        $cover_path = $book['cover'];


        // ==============================================
        // UPLOAD NEW COVER
        // ==============================================

        if (
            isset($_FILES['cover']) &&
            $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE
        ) {


            if (
                $_FILES['cover']['error'] !==
                UPLOAD_ERR_OK
            ) {

                $error =
                    "Gabim gjatë ngarkimit të fotos.";

            } else {


                $allowed_types = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/jpg'
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
                        "Lejohen vetëm JPG, PNG dhe WEBP.";

                } else {


                    // ==================================
                    // UPLOAD DIRECTORY
                    // ==================================

                    $upload_dir =
                        '../assets/uploads/';


                    if (
                        !is_dir($upload_dir)
                    ) {

                        mkdir(
                            $upload_dir,
                            0755,
                            true
                        );

                    }


                    // ==================================
                    // FILE EXTENSION
                    // ==================================

                    $extension =
                        strtolower(
                            pathinfo(
                                $_FILES['cover']['name'],
                                PATHINFO_EXTENSION
                            )
                        );


                    // ==================================
                    // UNIQUE NAME
                    // ==================================

                    $new_filename =
                        'book_' .
                        time() .
                        '_' .
                        uniqid() .
                        '.' .
                        $extension;


                    $new_file_path =
                        $upload_dir .
                        $new_filename;


                    // ==================================
                    // MOVE FILE
                    // ==================================

                    if (
                        move_uploaded_file(
                            $_FILES['cover']['tmp_name'],
                            $new_file_path
                        )
                    ) {


                        // Fshij cover-in e vjetër
                        // nëse ekziston

                        if (
                            !empty($book['cover'])
                        ) {

                            $old_cover =
                                '../' .
                                ltrim(
                                    $book['cover'],
                                    '/'
                                );


                            if (
                                file_exists(
                                    $old_cover
                                )
                            ) {

                                @unlink(
                                    $old_cover
                                );

                            }

                        }


                        $cover_path =
                            'assets/uploads/' .
                            $new_filename;


                    } else {

                        $error =
                            "Fotoja nuk mund të ruhej.";

                    }

                }

            }

        }


        // ==============================================
        // UPDATE DATABASE
        // ==============================================

        if ($error === '') {


            $update = mysqli_prepare(
                $conn,
                "UPDATE books
                 SET
                    title = ?,
                    price = ?,
                    author_id = ?,
                    category_id = ?,
                    cover = ?,
                    description = ?,
                    publish_date = ?,
                    featured = ?
                 WHERE id = ?"
            );


            mysqli_stmt_bind_param(
                $update,
                "sdiisssii",
                $title,
                $price,
                $author_id,
                $category_id,
                $cover_path,
                $description,
                $publish_date,
                $featured,
                $book_id
            );


            if (
                mysqli_stmt_execute($update)
            ) {


                $success =
                    "Libri u përditësua me sukses.";


                // Përditëso të dhënat
                // që shfaqen në formë

                $book['title'] =
                    $title;

                $book['price'] =
                    $price;

                $book['author_id'] =
                    $author_id;

                $book['category_id'] =
                    $category_id;

                $book['cover'] =
                    $cover_path;

                $book['description'] =
                    $description;

                $book['publish_date'] =
                    $publish_date;

                $book['featured'] =
                    $featured;


            } else {

                $error =
                    "Ndodhi një gabim gjatë përditësimit: " .
                    mysqli_error($conn);

            }


            mysqli_stmt_close($update);

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
        Ndrysho Librin | Jakup Ceraja Admin
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


    <!-- ==================================================
         SIDEBAR
    =================================================== -->

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



    <!-- ==================================================
         MAIN
    =================================================== -->

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
                    Ndrysho Librin
                </h1>

            </div>


            <div class="admin-topbar-actions">


                <a
                    href="books.php"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te Librat

                </a>

            </div>


        </header>



        <!-- ==================================================
             CONTENT
        =================================================== -->

        <div class="admin-content">


            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I LIBRAVE
                    </span>

                    <h2>
                        Ndrysho librin
                    </h2>

                    <p>
                        Përditëso të dhënat,
                        çmimin dhe kopertinën e librit.
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-pen-to-square"></i>

                </div>

            </div>



            <!-- ==================================================
                 MESSAGES
            =================================================== -->

            <?php if ($success !== ''): ?>

                <div class="admin-alert success">

                    <i class="fa-solid fa-circle-check"></i>

                    <?php
                    echo htmlspecialchars(
                        $success
                    );
                    ?>

                </div>

            <?php endif; ?>


            <?php if ($error !== ''): ?>

                <div class="admin-alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?php
                    echo htmlspecialchars(
                        $error
                    );
                    ?>

                </div>

            <?php endif; ?>



            <!-- ==================================================
                 FORM
            =================================================== -->

            <section class="admin-panel-card edit-book-panel">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            LIBRI #<?php
                            echo (int)$book['id'];
                            ?>
                        </span>

                        <h3>
                            Të dhënat e librit
                        </h3>

                    </div>

                </div>



                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="admin-form"
                >


                    <!-- TITLE -->

                    <div class="admin-form-group">

                        <label for="title">
                            Titulli i librit
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?php
                                echo htmlspecialchars(
                                    $book['title']
                                );
                            ?>"
                            required
                        >

                    </div>



                    <!-- PRICE -->

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
                                value="<?php
                                    echo htmlspecialchars(
                                        $book['price'] ?? '0.00'
                                    );
                                ?>"
                                required
                            >

                            <span>
                                €
                            </span>

                        </div>


                        <small class="form-help">
                            Vendos çmimin e librit në euro.
                        </small>

                    </div>



                    <!-- AUTHOR -->

                    <div class="admin-form-group">

                        <label for="author_id">
                            Autori
                        </label>

                        <select
                            id="author_id"
                            name="author_id"
                            required
                        >

                            <option value="">
                                Zgjidh autorin
                            </option>


                            <?php

                            if (
                                $authors_result &&
                                mysqli_num_rows(
                                    $authors_result
                                ) > 0
                            ):

                                while (
                                    $author =
                                    mysqli_fetch_assoc(
                                        $authors_result
                                    )
                                ):

                            ?>

                                <option
                                    value="<?php
                                        echo (int)$author['id'];
                                    ?>"
                                    <?php
                                    echo (
                                        (int)$book['author_id'] ===
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

                            <?php

                                endwhile;

                            endif;

                            ?>

                        </select>

                    </div>



                    <!-- CATEGORY -->

                    <div class="admin-form-group">

                        <label for="category_id">
                            Kategoria
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            required
                        >

                            <option value="">
                                Zgjidh kategorinë
                            </option>


                            <?php

                            if (
                                $categories_result &&
                                mysqli_num_rows(
                                    $categories_result
                                ) > 0
                            ):

                                while (
                                    $category =
                                    mysqli_fetch_assoc(
                                        $categories_result
                                    )
                                ):

                            ?>

                                <option
                                    value="<?php
                                        echo (int)$category['id'];
                                    ?>"
                                    <?php
                                    echo (
                                        (int)$book['category_id'] ===
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

                            <?php

                                endwhile;

                            endif;

                            ?>

                        </select>

                    </div>



                    <!-- PUBLISH DATE -->

                    <div class="admin-form-group">

                        <label for="publish_date">
                            Data e publikimit
                        </label>

                        <input
                            type="date"
                            id="publish_date"
                            name="publish_date"
                            value="<?php
                                echo htmlspecialchars(
                                    $book['publish_date'] ?? ''
                                );
                            ?>"
                        >

                    </div>



                    <!-- DESCRIPTION -->

                    <div class="admin-form-group">

                        <label for="description">
                            Përshkrimi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="7"
                            placeholder="Shkruani përshkrimin e librit..."
                        ><?php
                            echo htmlspecialchars(
                                $book['description'] ?? ''
                            );
                        ?></textarea>

                    </div>



                    <!-- CURRENT COVER -->

                    <div class="admin-form-group">

                        <label>
                            Kopertina aktuale
                        </label>


                        <?php if (!empty($book['cover'])): ?>

                            <div class="edit-book-current-cover">

                                <img
                                    src="../<?php
                                        echo htmlspecialchars(
                                            $book['cover']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $book['title']
                                        );
                                    ?>"
                                >

                            </div>

                        <?php else: ?>

                            <div class="edit-book-no-cover">

                                <i class="fa-solid fa-image"></i>

                                <span>
                                    Nuk ka kopertinë.
                                </span>

                            </div>

                        <?php endif; ?>

                    </div>



                    <!-- NEW COVER -->

                    <div class="admin-form-group">

                        <label for="cover">
                            Ndrysho kopertinën
                        </label>

                        <input
                            type="file"
                            id="cover"
                            name="cover"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small class="form-help">

                            Lëre bosh nëse dëshiron
                            ta mbash kopertinën aktuale.

                        </small>

                    </div>



                    <!-- FEATURED -->

                    <div class="admin-form-group">

                        <label class="checkbox-label">

                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                <?php
                                echo (
                                    (int)$book['featured'] === 1
                                )
                                ? 'checked'
                                : '';
                                ?>
                            >

                            <span>
                                Shfaqe si libër të veçuar
                            </span>

                        </label>

                    </div>



                    <!-- BUTTONS -->

                    <div class="edit-book-actions">


                        <a
                            href="books.php"
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

                            Ruaj ndryshimet

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