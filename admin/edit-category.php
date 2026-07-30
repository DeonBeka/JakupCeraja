<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET CATEGORY ID
// ==========================================

$category_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

if ($category_id <= 0) {

    header("Location: categories.php");
    exit;

}


// ==========================================
// GET CATEGORY
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, icon
     FROM categories
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $category_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$category = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$category) {

    header("Location: categories.php");
    exit;

}


$error = '';

$name = $category['name'];
$icon = $category['icon'];


// ==========================================
// UPDATE CATEGORY
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? '');


    if ($name === '') {

        $error =
            "Ju lutem shkruani emrin e kategorisë.";

    } else {


        // ==================================
        // CHECK DUPLICATE NAME
        // ==================================

        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM categories
             WHERE name = ?
             AND id != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check,
            "si",
            $name,
            $category_id
        );

        mysqli_stmt_execute($check);

        $check_result =
            mysqli_stmt_get_result($check);


        if (mysqli_num_rows($check_result) > 0) {

            $error =
                "Një kategori me këtë emër ekziston tashmë.";

        }


        mysqli_stmt_close($check);


        // ==================================
        // UPDATE
        // ==================================

        if ($error === '') {

            $update = mysqli_prepare(
                $conn,
                "UPDATE categories
                 SET name = ?, icon = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "ssi",
                $name,
                $icon,
                $category_id
            );


            if (mysqli_stmt_execute($update)) {

                mysqli_stmt_close($update);

                header(
                    "Location: categories.php?success=updated"
                );

                exit;

            } else {

                $error =
                    "Ndodhi një gabim gjatë përditësimit.";

                mysqli_stmt_close($update);

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
        Ndrysho Kategori | Jakup Ceraja Admin
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


            <a
                href="categories.php"
                class="active"
            >

                <i class="fa-solid fa-layer-group"></i>

                <span>Kategori</span>

            </a>


            <a href="news.php">

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
                    Ndrysho Kategori
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
                    href="categories.php"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te kategoritë

                </a>


            </div>


        </header>



        <!-- CONTENT -->

        <div class="admin-content">


            <div class="category-edit-page">


                <!-- HEADER -->

                <div class="admin-welcome">

                    <div>

                        <span>
                            MENAXHIMI I KATEGORIVE
                        </span>

                        <h2>
                            Ndrysho kategorinë
                        </h2>

                        <p>
                            Përditëso emrin ose ikonën e kategorisë.
                        </p>

                    </div>


                    <div class="admin-welcome-icon">

                        <i class="fa-solid fa-pen-to-square"></i>

                    </div>

                </div>



                <!-- ERROR -->

                <?php if ($error !== ''): ?>

                    <div class="category-form-message error">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </span>

                    </div>

                <?php endif; ?>



                <!-- FORM -->

                <section class="admin-panel-card category-form-card">


                    <div class="admin-panel-header">

                        <div>

                            <span>
                                PËRDITËSIMI
                            </span>

                            <h3>
                                Të dhënat e kategorisë
                            </h3>

                        </div>


                        <div class="category-edit-id">

                            ID #
                            <?php
                            echo $category_id;
                            ?>

                        </div>

                    </div>



                    <form
                        method="POST"
                        class="category-form"
                    >


                        <!-- NAME -->

                        <div class="category-form-field">

                            <label for="name">

                                Emri i kategorisë

                                <span>*</span>

                            </label>


                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?php
                                    echo htmlspecialchars($name);
                                ?>"
                                required
                            >


                            <small>
                                Emri që do të shfaqet në faqen e librave.
                            </small>

                        </div>



                        <!-- ICON -->

                        <div class="category-form-field">

                            <label for="icon">

                                Ikona e kategorisë

                            </label>


                            <input
                                type="text"
                                id="icon"
                                name="icon"
                                value="<?php
                                    echo htmlspecialchars($icon);
                                ?>"
                                placeholder="fa-solid fa-book"
                            >


                            <small>

                                Shembull:
                                <code>fa-solid fa-brain</code>

                            </small>



                            <!-- PREVIEW -->

                            <div class="category-icon-preview">

                                <div class="category-icon-preview-box">

                                    <i
                                        id="previewIcon"
                                        class="<?php
                                            echo htmlspecialchars(
                                                $icon ?: 'fa-solid fa-layer-group'
                                            );
                                        ?>"
                                    ></i>

                                </div>


                                <div>

                                    <strong>
                                        Parapamje e ikonës
                                    </strong>

                                    <span>
                                        Ikona ndryshon automatikisht.
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- ACTIONS -->

                        <div class="category-form-actions">


                            <a
                                href="categories.php"
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


        </div>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const input =
            document.getElementById('icon');

        const preview =
            document.getElementById('previewIcon');


        function updateIcon() {

            let value =
                input.value.trim();


            if (value === '') {

                value =
                    'fa-solid fa-layer-group';

            }


            preview.className = value;

        }


        input.addEventListener(
            'input',
            updateIcon
        );


        updateIcon();

    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>