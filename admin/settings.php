<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// Merr të dhënat e adminit aktual
$admin_id = $_SESSION['admin_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, fullname, username
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


// ======================================================
// UPDATE PROFILE
// ======================================================

if (isset($_POST['update_profile'])) {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');


    if ($fullname === '' || $username === '') {

        $error = "Ju lutem plotësoni të gjitha fushat.";

    } else {

        // Kontrollo nëse username është përdorur nga dikush tjetër
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

        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "Ky username është duke u përdorur.";

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

                $_SESSION['admin_fullname'] = $fullname;
                $_SESSION['admin_username'] = $username;

                $admin['fullname'] = $fullname;
                $admin['username'] = $username;

                $success = "Të dhënat u përditësuan me sukses.";

            } else {

                $error = "Ndodhi një gabim gjatë ruajtjes.";
            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($check);
    }
}


// ======================================================
// CHANGE PASSWORD
// ======================================================

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

    } elseif ($new_password !== $confirm_password) {

        $error =
            "Password-at e rinj nuk përputhen.";

    } elseif (strlen($new_password) < 8) {

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

    <title>Cilësimet | Admin Panel</title>

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


<body class="admin-page">


<div class="admin-layout">


    <!-- SIDEBAR -->

    <aside class="admin-sidebar">

        <div class="admin-sidebar-logo">

            <div class="admin-logo-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>

            <div>
                <strong>Jakup Ceraja</strong>
                <span>Admin Panel</span>
            </div>

        </div>


        <nav class="admin-sidebar-nav">

            <a href="dashboard.php">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>

            <a href="orders.php">
                <i class="fa-solid fa-box"></i>
                Porositë
            </a>

            <a href="settings.php" class="active">
                <i class="fa-solid fa-gear"></i>
                Cilësimet
            </a>

        </nav>


        <div class="admin-sidebar-bottom">

            <a href="../index.php">
                <i class="fa-solid fa-globe"></i>
                Shiko faqen
            </a>

            <a href="logout.php" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                Dil
            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">

            <div>

                <span class="admin-page-label">
                    ADMIN PANEL
                </span>

                <h1>
                    Cilësimet
                </h1>

            </div>


            <div class="admin-user">

                <div class="admin-user-icon">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $admin['fullname']
                        );
                        ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="admin-settings-content">


            <?php if ($success !== ''): ?>

                <div class="settings-message success">

                    <i class="fa-solid fa-circle-check"></i>

                    <?php
                    echo htmlspecialchars($success);
                    ?>

                </div>

            <?php endif; ?>


            <?php if ($error !== ''): ?>

                <div class="settings-message error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>


            <!-- PROFILE -->

            <div class="settings-card">

                <div class="settings-card-header">

                    <div class="settings-card-icon">

                        <i class="fa-solid fa-user"></i>

                    </div>

                    <div>

                        <h2>
                            Të dhënat e llogarisë
                        </h2>

                        <p>
                            Ndrysho emrin dhe username-in e administratorit.
                        </p>

                    </div>

                </div>


                <form method="POST">

                    <div class="settings-form-grid">


                        <div class="settings-field">

                            <label>
                                Emri i plotë
                            </label>

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


                        <div class="settings-field">

                            <label>
                                Username
                            </label>

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


                    <button
                        type="submit"
                        name="update_profile"
                        class="settings-save-button"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        Ruaj ndryshimet

                    </button>

                </form>

            </div>


            <!-- PASSWORD -->

            <div class="settings-card">

                <div class="settings-card-header">

                    <div class="settings-card-icon">

                        <i class="fa-solid fa-lock"></i>

                    </div>

                    <div>

                        <h2>
                            Ndrysho password-in
                        </h2>

                        <p>
                            Për siguri, përdor një password të fortë.
                        </p>

                    </div>

                </div>


                <form method="POST">


                    <div class="settings-password-grid">


                        <div class="settings-field">

                            <label>
                                Password aktual
                            </label>

                            <input
                                type="password"
                                name="current_password"
                                required
                            >

                        </div>


                        <div class="settings-field">

                            <label>
                                Password i ri
                            </label>

                            <input
                                type="password"
                                name="new_password"
                                required
                            >

                        </div>


                        <div class="settings-field">

                            <label>
                                Konfirmo password-in
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                required
                            >

                        </div>


                    </div>


                    <button
                        type="submit"
                        name="change_password"
                        class="settings-save-button"
                    >

                        <i class="fa-solid fa-key"></i>

                        Ndrysho password-in

                    </button>

                </form>

            </div>


        </section>

    </main>

</div>


</body>

</html>