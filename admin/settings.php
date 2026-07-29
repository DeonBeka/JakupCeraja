<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// ADMIN DATA
// ==========================================

$admin_id = $_SESSION['admin_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, fullname, username, password
     FROM admins
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $admin_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$admin = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$admin) {

    session_destroy();

    header("Location: login.php");

    exit;
}


$success = '';
$error = '';


// ==========================================
// UPDATE PROFILE
// ==========================================

if (isset($_POST['update_profile'])) {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');


    if ($fullname === '' || $username === '') {

        $error =
            "Ju lutem plotësoni të gjitha fushat.";

    } else {

        // Kontrollo username-in
        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM admins
             WHERE username = ?
             AND id != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check,
            "si",
            $username,
            $admin_id
        );

        mysqli_stmt_execute($check);

        $check_result =
            mysqli_stmt_get_result($check);


        if (mysqli_num_rows($check_result) > 0) {

            $error =
                "Ky username është duke u përdorur.";

        } else {

            $update = mysqli_prepare(
                $conn,
                "UPDATE admins
                 SET fullname = ?, username = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "ssi",
                $fullname,
                $username,
                $admin_id
            );


            if (mysqli_stmt_execute($update)) {

                $_SESSION['admin_fullname'] =
                    $fullname;

                $_SESSION['admin_username'] =
                    $username;

                $admin['fullname'] =
                    $fullname;

                $admin['username'] =
                    $username;

                $success =
                    "Të dhënat u përditësuan me sukses.";

            } else {

                $error =
                    "Ndodhi një gabim gjatë ruajtjes.";

            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($check);
    }
}


// ==========================================
// CHANGE PASSWORD
// ==========================================

if (isset($_POST['change_password'])) {

    $current_password =
        $_POST['current_password'] ?? '';

    $new_password =
        $_POST['new_password'] ?? '';

    $confirm_password =
        $_POST['confirm_password'] ?? '';


    if (
        $current_password === '' ||
        $new_password === '' ||
        $confirm_password === ''
    ) {

        $error =
            "Ju lutem plotësoni të gjitha fushat.";

    } elseif (
        $new_password !== $confirm_password
    ) {

        $error =
            "Password-at e rinj nuk përputhen.";

    } elseif (
        strlen($new_password) < 8
    ) {

        $error =
            "Password-i duhet të ketë së paku 8 karaktere.";

    } else {

        // Merr password-in aktual
        $stmt = mysqli_prepare(
            $conn,
            "SELECT password
             FROM admins
             WHERE id = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $admin_id
        );

        mysqli_stmt_execute($stmt);

        $result =
            mysqli_stmt_get_result($stmt);

        $account =
            mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);


        if (
            !$account ||
            !password_verify(
                $current_password,
                $account['password']
            )
        ) {

            $error =
                "Password-i aktual është gabim.";

        } else {

            $hashed_password =
                password_hash(
                    $new_password,
                    PASSWORD_DEFAULT
                );


            $update = mysqli_prepare(
                $conn,
                "UPDATE admins
                 SET password = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "si",
                $hashed_password,
                $admin_id
            );


            if (mysqli_stmt_execute($update)) {

                $success =
                    "Password-i u ndryshua me sukses.";

            } else {

                $error =
                    "Ndodhi një gabim gjatë ndryshimit të password-it.";

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
        Cilësime | Jakup Ceraja
    </title>


    <!-- ADMIN CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <!-- ANIMATIONS -->

    <link
        rel="stylesheet"
        href="../assets/css/animations.css"
    >


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


<!-- ==========================================
     ADMIN LAYOUT
=========================================== -->

<div class="admin-layout">


    <!-- ======================================
         SIDEBAR
    ======================================= -->

    <aside class="admin-sidebar">


        <!-- LOGO -->

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


        <!-- NAVIGATION -->

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


            <!-- ACTIVE -->

            <a
                href="settings.php"
                class="active"
            >

                <i class="fa-solid fa-gear"></i>

                <span>
                    Cilësime
                </span>

            </a>


        </nav>


        <!-- SIDEBAR BOTTOM -->

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


        <!-- ==================================
             TOPBAR
        =================================== -->

        <header class="admin-topbar">


            <!-- MOBILE MENU -->

            <button
                type="button"
                class="admin-menu-toggle"
                id="adminMenuToggle"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <!-- PAGE TITLE -->

            <div class="admin-topbar-title">

                <span>
                    ADMIN PANEL
                </span>

                <h1>
                    Cilësime
                </h1>

            </div>


            <!-- TOPBAR ACTIONS -->

            <div class="admin-topbar-actions">


                <!-- DARK MODE -->

                <button
                    type="button"
                    class="admin-icon-btn"
                    id="darkModeToggle"
                    title="Dark mode"
                >

                    <i class="fa-solid fa-moon"></i>

                </button>


                <!-- WEBSITE -->

                <a
                    href="../index.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Faqja

                </a>


            </div>


        </header>



        <!-- ==================================
             CONTENT
        =================================== -->

        <div class="admin-content">


            <!-- PAGE INTRO -->

            <div class="admin-welcome">

                <div>

                    <span>
                        LLOGARIA
                    </span>

                    <h2>
                        Cilësimet e administratorit
                    </h2>

                    <p>
                        Menaxho emrin, username-in dhe
                        password-in e llogarisë së administratorit.
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-user-gear"></i>

                </div>

            </div>



            <!-- =================================
                 MESSAGES
            ================================== -->


            <?php if ($success !== ''): ?>

                <div class="settings-alert settings-success">

                    <i class="fa-solid fa-circle-check"></i>

                    <span>

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </span>

                </div>

            <?php endif; ?>


            <?php if ($error !== ''): ?>

                <div class="settings-alert settings-error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </span>

                </div>

            <?php endif; ?>



            <!-- =================================
                 PROFILE SETTINGS
            ================================== -->

            <section class="admin-panel-card settings-card">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            PROFILI
                        </span>

                        <h3>
                            Të dhënat e llogarisë
                        </h3>

                    </div>


                    <div class="settings-section-icon">

                        <i class="fa-solid fa-user"></i>

                    </div>


                </div>



                <form method="POST">


                    <div class="settings-fields">


                        <!-- FULLNAME -->

                        <div class="settings-field">

                            <label>
                                Emri i plotë
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-user"></i>

                                <input
                                    type="text"
                                    name="fullname"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $admin['fullname']
                                    );
                                    ?>"
                                    required
                                >

                            </div>

                        </div>


                        <!-- USERNAME -->

                        <div class="settings-field">

                            <label>
                                Username
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-at"></i>

                                <input
                                    type="text"
                                    name="username"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $admin['username']
                                    );
                                    ?>"
                                    required
                                >

                            </div>

                        </div>


                    </div>


                    <div class="settings-form-footer">

                        <span>
                            Ndryshimet ruhen direkt në databazë.
                        </span>


                        <button
                            type="submit"
                            name="update_profile"
                            class="admin-primary-button"
                        >

                            <i class="fa-solid fa-floppy-disk"></i>

                            Ruaj ndryshimet

                        </button>

                    </div>


                </form>


            </section>



            <!-- =================================
                 PASSWORD SETTINGS
            ================================== -->

            <section class="admin-panel-card settings-card">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            SIGURIA
                        </span>

                        <h3>
                            Ndrysho password-in
                        </h3>

                    </div>


                    <div class="settings-section-icon">

                        <i class="fa-solid fa-lock"></i>

                    </div>


                </div>



                <form method="POST">


                    <div class="settings-fields settings-password-fields">


                        <!-- CURRENT -->

                        <div class="settings-field">

                            <label>
                                Password aktual
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-lock"></i>

                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                >

                            </div>

                        </div>


                        <!-- NEW -->

                        <div class="settings-field">

                            <label>
                                Password i ri
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-key"></i>

                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                >

                            </div>

                        </div>


                        <!-- CONFIRM -->

                        <div class="settings-field">

                            <label>
                                Konfirmo password-in
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-shield-halved"></i>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    required
                                >

                            </div>

                        </div>


                    </div>


                    <div class="settings-form-footer">

                        <span>
                            Password-i duhet të ketë së paku 8 karaktere.
                        </span>


                        <button
                            type="submit"
                            name="change_password"
                            class="admin-primary-button"
                        >

                            <i class="fa-solid fa-key"></i>

                            Ndrysho password-in

                        </button>

                    </div>


                </form>


            </section>



            <!-- =================================
                 ACCOUNT INFORMATION
            ================================== -->

            <section class="admin-panel-card settings-card">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            INFORMACION
                        </span>

                        <h3>
                            Llogaria aktuale
                        </h3>

                    </div>


                    <div class="settings-section-icon">

                        <i class="fa-solid fa-circle-info"></i>

                    </div>


                </div>



                <div class="settings-account-info">


                    <div class="settings-info-item">

                        <span>
                            ID e administratorit
                        </span>

                        <strong>
                            #<?php echo (int)$admin['id']; ?>
                        </strong>

                    </div>


                    <div class="settings-info-item">

                        <span>
                            Username aktual
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $admin['username']
                            );
                            ?>
                        </strong>

                    </div>


                    <div class="settings-info-item">

                        <span>
                            Emri
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $admin['fullname']
                            );
                            ?>
                        </strong>

                    </div>


                    <div class="settings-info-item">

                        <span>
                            Roli
                        </span>

                        <strong>
                            Administrator
                        </strong>

                    </div>


                </div>


            </section>


        </div>


    </main>


</div>



<!-- ==========================================
     JAVASCRIPT
=========================================== -->

<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>