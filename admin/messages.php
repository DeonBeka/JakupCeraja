<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET MESSAGES
// ==========================================

$sql = "
    SELECT
        id,
        fullname,
        email,
        phone,
        subject,
        message,
        status,
        created_at
    FROM messages
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}


$messages = [];

$total_messages = 0;
$unread_messages = 0;
$read_messages = 0;


while ($row = mysqli_fetch_assoc($result)) {

    $messages[] = $row;

    $total_messages++;

    if (
        strtolower($row['status']) === 'unread' ||
        strtolower($row['status']) === 'unread'
    ) {
        $unread_messages++;
    } else {
        $read_messages++;
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
        Mesazhet | Jakup Ceraja Admin
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


            <a href="gallery.php">

                <i class="fa-solid fa-images"></i>

                <span>Galeri</span>

            </a>


            <a
                href="messages.php"
                class="active"
            >

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
                    Mesazhet
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
                    href="../contact.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Kontakti

                </a>


            </div>


        </header>



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <!-- PAGE HEADER -->

            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I MESAZHEVE
                    </span>

                    <h2>
                        Mesazhet
                    </h2>

                    <p>
                        Shiko dhe menaxho mesazhet e dërguara
                        nga vizitorët e faqes.
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-envelope"></i>

                </div>

            </div>



            <!-- ==================================
                 STATS
            =================================== -->

            <div class="messages-stats">


                <div class="messages-stat-card">

                    <div class="messages-stat-icon">

                        <i class="fa-solid fa-envelope"></i>

                    </div>

                    <div>

                        <span>
                            Gjithsej mesazhe
                        </span>

                        <strong>
                            <?php echo $total_messages; ?>
                        </strong>

                    </div>

                </div>


                <div class="messages-stat-card unread-stat">

                    <div class="messages-stat-icon">

                        <i class="fa-solid fa-envelope-open"></i>

                    </div>

                    <div>

                        <span>
                            Të palexuara
                        </span>

                        <strong>
                            <?php echo $unread_messages; ?>
                        </strong>

                    </div>

                </div>


                <div class="messages-stat-card">

                    <div class="messages-stat-icon">

                        <i class="fa-solid fa-envelope-circle-check"></i>

                    </div>

                    <div>

                        <span>
                            Të lexuara
                        </span>

                        <strong>
                            <?php echo $read_messages; ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- ==================================
                 MESSAGES PANEL
            =================================== -->

            <section class="admin-panel-card messages-panel">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            INBOX
                        </span>

                        <h3>
                            Mesazhet e fundit
                        </h3>

                    </div>

                </div>



                <!-- SEARCH + FILTER -->

                <div class="messages-toolbar">


                    <div class="messages-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="messageSearch"
                            placeholder="Kërko emër, email ose subjekt..."
                            autocomplete="off"
                        >

                    </div>


                    <select
                        id="messageFilter"
                        class="messages-filter"
                    >

                        <option value="all">
                            Të gjitha
                        </option>

                        <option value="unread">
                            Të palexuara
                        </option>

                        <option value="read">
                            Të lexuara
                        </option>

                    </select>


                </div>



                <!-- ==================================
                     MESSAGE LIST
                =================================== -->

                <div class="messages-list">


                <?php if (empty($messages)): ?>


                    <div class="messages-empty">

                        <div class="messages-empty-icon">

                            <i class="fa-solid fa-envelope-open"></i>

                        </div>

                        <h3>
                            Nuk ka mesazhe
                        </h3>

                        <p>
                            Aktualisht nuk ka mesazhe të dërguara.
                        </p>

                    </div>


                <?php else: ?>


                    <?php foreach ($messages as $message): ?>


                        <?php

                        $status =
                            strtolower(
                                trim($message['status'] ?? '')
                            );

                        $is_unread =
                            $status === 'unread' ||
                            $status === 'unread';

                        ?>


                        <article
                            class="message-item <?php
                                echo $is_unread
                                    ? 'unread'
                                    : 'read';
                            ?>"

                            data-status="<?php
                                echo $is_unread
                                    ? 'unread'
                                    : 'read';
                            ?>"

                            data-search="<?php
                                echo htmlspecialchars(
                                    strtolower(
                                        $message['fullname']
                                        . ' '
                                        . $message['email']
                                        . ' '
                                        . $message['subject']
                                    )
                                );
                            ?>"
                        >


                            <!-- ICON -->

                            <div class="message-avatar">

                                <i class="fa-solid fa-user"></i>

                            </div>



                            <!-- CONTENT -->

                            <div class="message-main">


                                <div class="message-top">


                                    <div>

                                        <strong class="message-name">

                                            <?php
                                            echo htmlspecialchars(
                                                $message['fullname']
                                            );
                                            ?>

                                        </strong>


                                        <span class="message-email">

                                            <?php
                                            echo htmlspecialchars(
                                                $message['email']
                                            );
                                            ?>

                                        </span>

                                    </div>


                                    <span class="message-date">

                                        <?php

                                        echo date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $message['created_at']
                                            )
                                        );

                                        ?>

                                    </span>


                                </div>



                                <div class="message-subject">

                                    <?php
                                    echo htmlspecialchars(
                                        $message['subject']
                                    );
                                    ?>

                                </div>


                                <p class="message-preview">

                                    <?php

                                    $text =
                                        $message['message'];

                                    echo htmlspecialchars(
                                        mb_substr(
                                            $text,
                                            0,
                                            160
                                        )
                                    );

                                    if (
                                        mb_strlen($text) > 160
                                    ) {

                                        echo '...';

                                    }

                                    ?>

                                </p>



                                <!-- CONTACT -->

                                <div class="message-contact-info">


                                    <?php if (
                                        !empty(
                                            $message['phone']
                                        )
                                    ): ?>

                                        <span>

                                            <i class="fa-solid fa-phone"></i>

                                            <?php
                                            echo htmlspecialchars(
                                                $message['phone']
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>


                                    <span>

                                        <i class="fa-solid fa-envelope"></i>

                                        <?php
                                        echo htmlspecialchars(
                                            $message['email']
                                        );
                                        ?>

                                    </span>


                                </div>


                            </div>



                            <!-- ACTIONS -->

                            <div class="message-actions">


                                <a
                                    href="view-message.php?id=<?php
                                        echo (int)$message['id'];
                                    ?>"
                                    class="message-action view"
                                    title="Shiko mesazhin"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                </a>


                                <a
                                    href="delete-message.php?id=<?php
                                        echo (int)$message['id'];
                                    ?>"
                                    class="message-action delete"
                                    title="Fshi mesazhin"
                                    onclick="
                                        return confirm(
                                            'A jeni i sigurt që dëshironi ta fshini këtë mesazh?'
                                        );
                                    "
                                >

                                    <i class="fa-solid fa-trash"></i>

                                </a>


                            </div>


                        </article>


                    <?php endforeach; ?>


                <?php endif; ?>


                </div>


                <div
                    class="messages-no-results"
                    id="messagesNoResults"
                >

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <h3>
                        Nuk u gjet asnjë mesazh
                    </h3>

                    <p>
                        Provo një kërkim ose filtër tjetër.
                    </p>

                </div>


            </section>


        </div>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const search =
            document.getElementById(
                'messageSearch'
            );

        const filter =
            document.getElementById(
                'messageFilter'
            );

        const items =
            document.querySelectorAll(
                '.message-item'
            );

        const noResults =
            document.getElementById(
                'messagesNoResults'
            );


        function filterMessages() {

            const searchValue =
                search.value
                    .toLowerCase()
                    .trim();

            const filterValue =
                filter.value;


            let visible = 0;


            items.forEach(function (item) {


                const text =
                    item.dataset.search || '';

                const status =
                    item.dataset.status || '';


                const matchesSearch =
                    text.includes(
                        searchValue
                    );


                const matchesFilter =
                    filterValue === 'all' ||
                    filterValue === status;


                if (
                    matchesSearch &&
                    matchesFilter
                ) {

                    item.style.display = '';

                    visible++;

                } else {

                    item.style.display = 'none';

                }

            });


            if (
                visible === 0 &&
                items.length > 0
            ) {

                noResults.style.display = 'block';

            } else {

                noResults.style.display = 'none';

            }

        }


        search.addEventListener(
            'input',
            filterMessages
        );


        filter.addEventListener(
            'change',
            filterMessages
        );


    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>