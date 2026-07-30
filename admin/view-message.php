<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET MESSAGE ID
// ==========================================

$message_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


// ==========================================
// CHECK ID
// ==========================================

if ($message_id <= 0) {

    header("Location: messages.php");
    exit;

}


// ==========================================
// MARK AS READ
// ==========================================

$update_status = mysqli_prepare(
    $conn,
    "UPDATE messages
     SET status = 'read'
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $update_status,
    "i",
    $message_id
);

mysqli_stmt_execute($update_status);

mysqli_stmt_close($update_status);


// ==========================================
// GET MESSAGE
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        fullname,
        email,
        phone,
        subject,
        message,
        status,
        created_at
     FROM messages
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $message_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$message = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// ==========================================
// MESSAGE NOT FOUND
// ==========================================

if (!$message) {

    header("Location: messages.php");
    exit;

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
        Shiko Mesazhin | Jakup Ceraja Admin
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


            <a href="gallery.php">

                <i class="fa-solid fa-images"></i>

                <span>
                    Galeri
                </span>

            </a>


            <a
                href="messages.php"
                class="active"
            >

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
                    Shiko mesazhin
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



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <div class="message-view-page">


                <!-- BACK -->

                <a
                    href="messages.php"
                    class="message-back-button"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kthehu te mesazhet

                </a>



                <!-- MESSAGE CARD -->

                <section class="message-view-card">


                    <!-- HEADER -->

                    <div class="message-view-header">


                        <div class="message-view-person">


                            <div class="message-view-avatar">

                                <i class="fa-solid fa-user"></i>

                            </div>


                            <div>

                                <h2>

                                    <?php
                                    echo htmlspecialchars(
                                        $message['fullname']
                                    );
                                    ?>

                                </h2>

                                <span>

                                    <?php
                                    echo htmlspecialchars(
                                        $message['email']
                                    );
                                    ?>

                                </span>

                            </div>


                        </div>


                        <div class="message-view-date">

                            <i class="fa-regular fa-calendar"></i>

                            <?php

                            echo date(
                                'd.m.Y H:i',
                                strtotime(
                                    $message['created_at']
                                )
                            );

                            ?>

                        </div>


                    </div>



                    <!-- SUBJECT -->

                    <div class="message-view-subject">

                        <span>
                            SUBJEKTI
                        </span>

                        <h1>

                            <?php
                            echo htmlspecialchars(
                                $message['subject']
                            );
                            ?>

                        </h1>

                    </div>



                    <!-- CONTACT INFO -->

                    <div class="message-view-contact">


                        <div>

                            <i class="fa-solid fa-envelope"></i>

                            <div>

                                <span>
                                    Email
                                </span>

                                <strong>

                                    <a
                                        href="mailto:<?php
                                            echo htmlspecialchars(
                                                $message['email']
                                            );
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $message['email']
                                        );
                                        ?>

                                    </a>

                                </strong>

                            </div>

                        </div>



                        <?php if (!empty($message['phone'])): ?>

                            <div>

                                <i class="fa-solid fa-phone"></i>

                                <div>

                                    <span>
                                        Telefoni
                                    </span>

                                    <strong>

                                        <a
                                            href="tel:<?php
                                                echo htmlspecialchars(
                                                    $message['phone']
                                                );
                                            ?>"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $message['phone']
                                            );
                                            ?>

                                        </a>

                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>


                        <div>

                            <i class="fa-solid fa-circle-check"></i>

                            <div>

                                <span>
                                    Statusi
                                </span>

                                <strong class="message-read-status">

                                    <i class="fa-solid fa-envelope-open"></i>

                                    Lexuar

                                </strong>

                            </div>

                        </div>


                    </div>



                    <!-- MESSAGE -->

                    <div class="message-view-body">

                        <span>
                            MESAZHI
                        </span>

                        <div class="message-view-text">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $message['message']
                                )
                            );

                            ?>

                        </div>

                    </div>



                    <!-- ACTIONS -->

                    <div class="message-view-actions">


                        <a
                            href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php
                                echo urlencode($message['email']);
                                ?>&su=<?php
                                echo urlencode('Re: ' . $message['subject']);
                            ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="admin-primary-button"
                            >
                            <i class="fa-solid fa-reply"></i>

                            Përgjigju me email
                        </a>


                        <a
                            href="delete-message.php?id=<?php
                                echo (int)$message['id'];
                            ?>"
                            class="message-delete-button"
                            onclick="
                                return confirm(
                                    'A jeni i sigurt që dëshironi ta fshini këtë mesazh?'
                                );
                            "
                        >

                            <i class="fa-solid fa-trash"></i>

                            Fshi mesazhin

                        </a>


                    </div>


                </section>


            </div>


        </div>


    </main>


</div>



<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>