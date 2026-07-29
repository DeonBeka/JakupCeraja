<?php


require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// STATISTICS
// ==========================================

function getCount($conn, $table)
{
    $allowed_tables = [
        'books',
        'authors',
        'categories',
        'news',
        'gallery',
        'messages',
        'orders'
    ];

    if (!in_array($table, $allowed_tables)) {
        return 0;
    }

    $result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM `$table`"
    );

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return (int)$row['total'];
}


$books_count = getCount($conn, 'books');
$authors_count = getCount($conn, 'authors');
$categories_count = getCount($conn, 'categories');
$news_count = getCount($conn, 'news');
$gallery_count = getCount($conn, 'gallery');
$messages_count = getCount($conn, 'messages');


// Orders table might not exist yet
$orders_count = 0;

$orders_check = mysqli_query(
    $conn,
    "SHOW TABLES LIKE 'orders'"
);

if ($orders_check && mysqli_num_rows($orders_check) > 0) {
    $orders_count = getCount($conn, 'orders');
}


// ==========================================
// RECENT BOOKS
// ==========================================

$recent_books = [];

$books_query = mysqli_query(
    $conn,
    "
    SELECT
        books.id,
        books.title,
        books.cover,
        books.created_at,
        authors.fullname AS author_name

    FROM books

    LEFT JOIN authors
        ON books.author_id = authors.id

    ORDER BY books.created_at DESC

    LIMIT 5
    "
);

if ($books_query) {

    while ($row = mysqli_fetch_assoc($books_query)) {
        $recent_books[] = $row;
    }

}


// ==========================================
// RECENT NEWS
// ==========================================

$recent_news = [];

$news_query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        title,
        created_at

    FROM news

    ORDER BY created_at DESC

    LIMIT 5
    "
);

if ($news_query) {

    while ($row = mysqli_fetch_assoc($news_query)) {
        $recent_news[] = $row;
    }

}


// ==========================================
// RECENT ORDERS
// ==========================================

$recent_orders = [];

if ($orders_count > 0) {

    $orders_query = mysqli_query(
        $conn,
        "
        SELECT
            orders.id,
            orders.fullname,
            orders.total_price,
            orders.status,
            orders.created_at,
            books.title AS book_title

        FROM orders

        LEFT JOIN books
            ON orders.book_id = books.id

        ORDER BY orders.created_at DESC

        LIMIT 5
        "
    );

    if ($orders_query) {

        while ($row = mysqli_fetch_assoc($orders_query)) {
            $recent_orders[] = $row;
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
        Dashboard | Jakup Ceraja
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


<!-- ==========================================
     ADMIN LAYOUT
=========================================== -->

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


        <!-- NAVIGATION -->

        <nav class="admin-nav">


            <a
                href="dashboard.php"
                class="active"
            >

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

                <?php if ($messages_count > 0): ?>

                    <span class="nav-badge">
                        <?php echo $messages_count; ?>
                    </span>

                <?php endif; ?>

            </a>


            <a href="orders.php">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Porosi
                </span>

                <?php if ($orders_count > 0): ?>

                    <span class="nav-badge">
                        <?php echo $orders_count; ?>
                    </span>

                <?php endif; ?>

            </a>


            <a href="settings.php">

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


            <button
                type="button"
                class="admin-menu-toggle"
                id="adminMenuToggle"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div class="admin-topbar-title">

                <span>
                    Mirë se vini
                </span>

                <h1>
                    Dashboard
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


            <!-- WELCOME -->

            <div class="admin-welcome">

                <div>

                    <span>
                        SHTËPIA BOTUESE "JAKUP CERAJA"
                    </span>

                    <h2>
                        Menaxho faqen tuaj nga një vend.
                    </h2>

                    <p>
                        Këtu mund të menaxhoni librat,
                        autorët, lajmet, galerinë dhe porositë.
                    </p>

                </div>

                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-book-open"></i>

                </div>

            </div>



            <!-- =================================
                 STATISTICS
            ================================== -->

            <section class="admin-stats">


                <!-- BOOKS -->

                <div class="admin-stat-card">

                    <div class="stat-icon books">

                        <i class="fa-solid fa-book"></i>

                    </div>

                    <div>

                        <span>
                            Libra
                        </span>

                        <strong>
                            <?php echo $books_count; ?>
                        </strong>

                    </div>

                    <a href="books.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- AUTHORS -->

                <div class="admin-stat-card">

                    <div class="stat-icon authors">

                        <i class="fa-solid fa-pen-nib"></i>

                    </div>

                    <div>

                        <span>
                            Autorë
                        </span>

                        <strong>
                            <?php echo $authors_count; ?>
                        </strong>

                    </div>

                    <a href="authors.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- CATEGORIES -->

                <div class="admin-stat-card">

                    <div class="stat-icon categories">

                        <i class="fa-solid fa-layer-group"></i>

                    </div>

                    <div>

                        <span>
                            Kategori
                        </span>

                        <strong>
                            <?php echo $categories_count; ?>
                        </strong>

                    </div>

                    <a href="categories.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- NEWS -->

                <div class="admin-stat-card">

                    <div class="stat-icon news">

                        <i class="fa-solid fa-newspaper"></i>

                    </div>

                    <div>

                        <span>
                            Lajme
                        </span>

                        <strong>
                            <?php echo $news_count; ?>
                        </strong>

                    </div>

                    <a href="news.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- GALLERY -->

                <div class="admin-stat-card">

                    <div class="stat-icon gallery">

                        <i class="fa-solid fa-images"></i>

                    </div>

                    <div>

                        <span>
                            Galeri
                        </span>

                        <strong>
                            <?php echo $gallery_count; ?>
                        </strong>

                    </div>

                    <a href="gallery.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- MESSAGES -->

                <div class="admin-stat-card">

                    <div class="stat-icon messages">

                        <i class="fa-solid fa-envelope"></i>

                    </div>

                    <div>

                        <span>
                            Mesazhe
                        </span>

                        <strong>
                            <?php echo $messages_count; ?>
                        </strong>

                    </div>

                    <a href="messages.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <!-- ORDERS -->

                <div class="admin-stat-card">

                    <div class="stat-icon orders">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>

                    <div>

                        <span>
                            Porosi
                        </span>

                        <strong>
                            <?php echo $orders_count; ?>
                        </strong>

                    </div>

                    <a href="orders.php">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </section>



            <!-- =================================
                 LOWER GRID
            ================================== -->

            <div class="admin-dashboard-grid">


                <!-- =================================
                     RECENT BOOKS
                ================================== -->

                <section class="admin-panel-card">


                    <div class="admin-panel-header">

                        <div>

                            <span>
                                PËRMBAJTJA
                            </span>

                            <h3>
                                Librat e fundit
                            </h3>

                        </div>

                        <a href="books.php">
                            Shiko të gjithë
                        </a>

                    </div>


                    <?php if (!empty($recent_books)): ?>

                        <div class="admin-recent-list">

                            <?php foreach ($recent_books as $book): ?>

                                <div class="admin-recent-item">


                                    <div class="recent-book-cover">

                                        <?php if (!empty($book['cover'])): ?>

                                            <img
                                                src="../assets/uploads/<?php echo htmlspecialchars($book['cover']); ?>"
                                                alt=""
                                            >

                                        <?php else: ?>

                                            <i class="fa-solid fa-book"></i>

                                        <?php endif; ?>

                                    </div>


                                    <div class="recent-item-info">

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $book['title']
                                            );
                                            ?>

                                        </strong>

                                        <span>

                                            <?php
                                            echo !empty($book['author_name'])
                                                ? htmlspecialchars(
                                                    $book['author_name']
                                                )
                                                : 'Autor i panjohur';
                                            ?>

                                        </span>

                                    </div>


                                    <a
                                        href="edit-book.php?id=<?php echo (int)$book['id']; ?>"
                                        class="recent-item-action"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="admin-empty-state">

                            <i class="fa-solid fa-book-open"></i>

                            <p>
                                Ende nuk ka libra.
                            </p>

                            <a href="add-book.php">
                                Shto librin e parë
                            </a>

                        </div>

                    <?php endif; ?>

                </section>



                <!-- =================================
                     RECENT NEWS
                ================================== -->

                <section class="admin-panel-card">


                    <div class="admin-panel-header">

                        <div>

                            <span>
                                LAJMET
                            </span>

                            <h3>
                                Lajmet e fundit
                            </h3>

                        </div>

                        <a href="news.php">
                            Shiko të gjithë
                        </a>

                    </div>


                    <?php if (!empty($recent_news)): ?>

                        <div class="admin-news-list">

                            <?php foreach ($recent_news as $item): ?>

                                <a
                                    href="news.php"
                                    class="admin-news-item"
                                >

                                    <div class="news-date">

                                        <?php

                                        echo date(
                                            'd',
                                            strtotime(
                                                $item['created_at']
                                            )
                                        );

                                        ?>

                                        <small>

                                            <?php

                                            echo date(
                                                'M',
                                                strtotime(
                                                    $item['created_at']
                                                )
                                            );

                                            ?>

                                        </small>

                                    </div>


                                    <div>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $item['title']
                                            );
                                            ?>

                                        </strong>

                                        <span>
                                            Shiko lajmin
                                        </span>

                                    </div>


                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="admin-empty-state">

                            <i class="fa-solid fa-newspaper"></i>

                            <p>
                                Ende nuk ka lajme.
                            </p>

                            <a href="news.php">
                                Shto lajmin e parë
                            </a>

                        </div>

                    <?php endif; ?>

                </section>


            </div>



            <!-- =================================
                 RECENT ORDERS
            ================================== -->

            <section class="admin-panel-card admin-orders-panel">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            POROSITË
                        </span>

                        <h3>
                            Porositë e fundit
                        </h3>

                    </div>

                    <a href="orders.php">
                        Menaxho porositë
                    </a>

                </div>


                <?php if (!empty($recent_orders)): ?>

                    <div class="admin-orders-table-wrapper">

                        <table class="admin-orders-table">

                            <thead>

                                <tr>

                                    <th>
                                        Klienti
                                    </th>

                                    <th>
                                        Libri
                                    </th>

                                    <th>
                                        Totali
                                    </th>

                                    <th>
                                        Statusi
                                    </th>

                                    <th>
                                        Data
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($recent_orders as $order): ?>

                                    <tr>

                                        <td>

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $order['fullname']
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $order['book_title']
                                                ?? 'Libër i fshirë'
                                            );
                                            ?>

                                        </td>


                                        <td>

                                            <strong>

                                                <?php
                                                echo number_format(
                                                    (float)$order['total_price'],
                                                    2
                                                );
                                                ?> €

                                            </strong>

                                        </td>


                                        <td>

                                            <?php

                                            $status = $order['status'];

                                            $status_names = [

                                                'new' =>
                                                    'E re',

                                                'confirmed' =>
                                                    'E konfirmuar',

                                                'shipped' =>
                                                    'Dërguar',

                                                'completed' =>
                                                    'Përfunduar',

                                                'cancelled' =>
                                                    'Anuluar'

                                            ];

                                            ?>

                                            <span
                                                class="order-status <?php echo htmlspecialchars($status); ?>"
                                            >

                                                <?php

                                                echo htmlspecialchars(
                                                    $status_names[$status]
                                                    ?? $status
                                                );

                                                ?>

                                            </span>

                                        </td>


                                        <td>

                                            <?php

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $order['created_at']
                                                )
                                            );

                                            ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="admin-empty-state">

                        <i class="fa-solid fa-cart-shopping"></i>

                        <p>
                            Ende nuk ka porosi.
                        </p>

                        <span>
                            Porositë e klientëve do të shfaqen këtu.
                        </span>

                    </div>

                <?php endif; ?>

            </section>


        </div>

    </main>

</div>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>

</body>
</html>