<?php

session_start();

require_once '../includes/database.php';


// Nëse është tashmë i kyçur
if (
    isset($_SESSION['admin_logged_in']) &&
    $_SESSION['admin_logged_in'] === true
) {
    header("Location: dashboard.php");
    exit;
}


$error = '';


// LOGIN

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($username === '' || $password === '') {

        $error = "Ju lutem plotësoni të gjitha fushat.";

    } else {

        // Kërko adminin në databazë
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, fullname, username, password
             FROM admins
             WHERE username = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $admin = mysqli_fetch_assoc($result);


        // Kontrollo password-in
        if (
            $admin &&
            password_verify(
                $password,
                $admin['password']
            )
        ) {

            // Siguri shtesë për session
            session_regenerate_id(true);


            $_SESSION['admin_logged_in'] = true;

            $_SESSION['admin_id'] =
                $admin['id'];

            $_SESSION['admin_fullname'] =
                $admin['fullname'];

            $_SESSION['admin_username'] =
                $admin['username'];


            mysqli_stmt_close($stmt);


            header("Location: dashboard.php");

            exit;

        } else {

            $error =
                "Username ose password është gabim.";

        }


        mysqli_stmt_close($stmt);

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
        Admin Login | Jakup Ceraja
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


<body class="admin-login-page">


<div class="admin-login-wrapper">


    <div class="admin-login-card">


        <!-- LOGO -->

        <div class="admin-login-logo">

            <div class="admin-login-icon">

                <i class="fa-solid fa-book-open"></i>

            </div>


            <div>

                <strong>
                    Jakup Ceraja
                </strong>

                <span>
                    Shtëpia Botuese
                </span>

            </div>

        </div>


        <!-- TITLE -->

        <div class="admin-login-heading">

            <span>
                ADMIN PANEL
            </span>

            <h1>
                Mirë se vini
            </h1>

            <p>
                Kyçuni në panelin administrativ.
            </p>

        </div>


        <!-- ERROR -->

        <?php if ($error !== ''): ?>

            <div class="admin-login-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- FORM -->

        <form
            method="POST"
            action="login.php"
            class="admin-login-form"
        >


            <div class="admin-login-field">

                <label for="username">
                    Username
                </label>


                <div class="admin-input-wrapper">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Shkruani username"
                        autocomplete="username"
                        required
                    >

                </div>

            </div>


            <div class="admin-login-field">

                <label for="password">
                    Password
                </label>


                <div class="admin-input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Shkruani password"
                        autocomplete="current-password"
                        required
                    >


                    <button
                        type="button"
                        class="password-toggle"
                        id="passwordToggle"
                        aria-label="Shfaq password"
                    >

                        <i class="fa-solid fa-eye"></i>

                    </button>

                </div>

            </div>


            <button
                type="submit"
                class="admin-login-button"
            >

                <span>
                    Kyçu
                </span>

                <i class="fa-solid fa-arrow-right"></i>

            </button>


        </form>


        <a
            href="../index.php"
            class="admin-back-site"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Kthehu në faqe

        </a>


    </div>


</div>


<script>

const passwordInput =
    document.getElementById('password');

const passwordToggle =
    document.getElementById('passwordToggle');


if (passwordToggle && passwordInput) {

    passwordToggle.addEventListener(
        'click',
        function () {

            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                this.innerHTML =
                    '<i class="fa-solid fa-eye-slash"></i>';

            } else {

                passwordInput.type = 'password';

                this.innerHTML =
                    '<i class="fa-solid fa-eye"></i>';

            }

        }
    );

}

</script>


</body>

</html>