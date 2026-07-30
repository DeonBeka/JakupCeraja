<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET BOOKS
// ==========================================

$sql = "
    SELECT
        books.id,
        books.title,
        books.cover,
        books.description,
        books.publish_date,
        books.featured,
        books.created_at,

        authors.fullname AS author_name,

        categories.name AS category_name

    FROM books

    LEFT JOIN authors
        ON books.author_id = authors.id

    LEFT JOIN categories
        ON books.category_id = categories.id

    ORDER BY books.created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}


// ==========================================
// STATISTICS
// ==========================================

$total_books = 0;
$featured_books = 0;

$books = [];

while ($book = mysqli_fetch_assoc($result)) {

    $books[] = $book;

    $total_books++;

    if ((int)$book['featured'] === 1) {
        $featured_books++;
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
        Librat | Jakup Ceraja Admin
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
                    ADMIN PANEL
                </span>

                <h1>
                    Librat
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
                    href="../books.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Shiko librat

                </a>


            </div>


        </header>



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <!-- HEADER -->

            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I LIBRAVE
                    </span>

                    <h2>
                        Librat
                    </h2>

                    <p>
                        Shto, ndrysho dhe menaxho librat
                        e Shtëpisë Botuese "Jakup Ceraja".
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-book"></i>

                </div>

            </div>



            <!-- ==================================
                 STATS
            =================================== -->

            <div class="books-stats">


                <div class="books-stat-card">

                    <div class="books-stat-icon">

                        <i class="fa-solid fa-book"></i>

                    </div>

                    <div>

                        <span>
                            Gjithsej libra
                        </span>

                        <strong>
                            <?php echo $total_books; ?>
                        </strong>

                    </div>

                </div>


                <div class="books-stat-card">

                    <div class="books-stat-icon">

                        <i class="fa-solid fa-star"></i>

                    </div>

                    <div>

                        <span>
                            Libra të veçuar
                        </span>

                        <strong>
                            <?php echo $featured_books; ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- ==================================
                 BOOKS TABLE
            =================================== -->

            <section class="admin-panel-card books-panel">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            LIBRAT
                        </span>

                        <h3>
                            Lista e librave
                        </h3>

                    </div>


                    <a
                        href="add-book.php"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Shto libër

                    </a>


                </div>



                <!-- SEARCH -->

                <div class="books-toolbar">


                    <div class="books-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="bookSearch"
                            placeholder="Kërko libër..."
                        >

                    </div>


                    <select
                        id="bookFilter"
                        class="books-filter"
                    >

                        <option value="all">
                            Të gjithë librat
                        </option>

                        <option value="featured">
                            Të veçuar
                        </option>

                        <option value="normal">
                            Të zakonshëm
                        </option>

                    </select>


                </div>



                <!-- TABLE -->

                <div class="books-table-wrapper">

                    <table class="books-table">

                        <thead>

                            <tr>

                                <th>
                                    Libri
                                </th>

                                <th>
                                    Autori
                                </th>

                                <th>
                                    Kategoria
                                </th>

                                <th>
                                    Data
                                </th>

                                <th>
                                    Statusi
                                </th>

                                <th>
                                    Veprime
                                </th>

                            </tr>

                        </thead>


                        <tbody id="booksTableBody">


                        <?php if (empty($books)): ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="books-empty"
                                >

                                    <i class="fa-solid fa-book-open"></i>

                                    <strong>
                                        Nuk ka libra ende
                                    </strong>

                                    <span>
                                        Shto librin e parë nga butoni
                                        "Shto libër".
                                    </span>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($books as $book): ?>


                                <tr
                                    data-title="<?php
                                        echo htmlspecialchars(
                                            strtolower(
                                                $book['title']
                                            )
                                        );
                                    ?>"
                                    data-status="<?php
                                        echo (
                                            (int)$book['featured'] === 1
                                            ? 'featured'
                                            : 'normal'
                                        );
                                    ?>"
                                >


                                    <!-- BOOK -->

                                    <td>

                                        <div class="book-table-info">


                                            <?php if (
                                                !empty($book['cover'])
                                            ): ?>

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
                                                    class="book-cover-small"
                                                >

                                            <?php else: ?>

                                                <div class="book-cover-placeholder">

                                                    <i class="fa-solid fa-book"></i>

                                                </div>

                                            <?php endif; ?>


                                            <div>

                                                <strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $book['title']
                                                    );
                                                    ?>

                                                </strong>

                                                <span>

                                                    ID #<?php
                                                    echo (int)$book['id'];
                                                    ?>

                                                </span>

                                            </div>


                                        </div>

                                    </td>



                                    <!-- AUTHOR -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $book['author_name']
                                            ?: 'Pa autor'
                                        );

                                        ?>

                                    </td>



                                    <!-- CATEGORY -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $book['category_name']
                                            ?: 'Pa kategori'
                                        );

                                        ?>

                                    </td>



                                    <!-- DATE -->

                                    <td>

                                        <?php

                                        if (
                                            !empty(
                                                $book['publish_date']
                                            )
                                        ) {

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $book['publish_date']
                                                )
                                            );

                                        } else {

                                            echo '-';

                                        }

                                        ?>

                                    </td>



                                    <!-- STATUS -->

                                    <td>


                                        <?php if (
                                            (int)$book['featured'] === 1
                                        ): ?>

                                            <span class="book-status featured">

                                                <i class="fa-solid fa-star"></i>

                                                I veçuar

                                            </span>

                                        <?php else: ?>

                                            <span class="book-status normal">

                                                Publik

                                            </span>

                                        <?php endif; ?>


                                    </td>



                                    <!-- ACTIONS -->

                                    <td>

                                        <div class="book-actions">


                                            <a
                                                href="edit-book.php?id=<?php
                                                    echo (int)$book['id'];
                                                ?>"
                                                class="book-action edit"
                                                title="Ndrysho"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                            </a>


                                            <a
                                                href="delete-book.php?id=<?php
                                                    echo (int)$book['id'];
                                                ?>"
                                                class="book-action delete"
                                                title="Fshi"
                                                onclick="
                                                    return confirm(
                                                        'A jeni i sigurt që dëshironi ta fshini këtë libër?'
                                                    );
                                                "
                                            >

                                                <i class="fa-solid fa-trash"></i>

                                            </a>


                                        </div>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php endif; ?>


                        </tbody>

                    </table>

                </div>


            </section>


        </div>


    </main>


</div>



<!-- ======================================
     JAVASCRIPT
======================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const search =
            document.getElementById('bookSearch');

        const filter =
            document.getElementById('bookFilter');

        const rows =
            document.querySelectorAll(
                '#booksTableBody tr[data-title]'
            );


        function filterBooks() {

            const searchValue =
                search.value
                    .toLowerCase()
                    .trim();

            const filterValue =
                filter.value;


            rows.forEach(function (row) {


                const title =
                    row.dataset.title || '';

                const status =
                    row.dataset.status || '';


                const matchesSearch =
                    title.includes(searchValue);


                const matchesFilter =
                    filterValue === 'all' ||
                    filterValue === status;


                if (
                    matchesSearch &&
                    matchesFilter
                ) {

                    row.style.display = '';

                } else {

                    row.style.display = 'none';

                }

            });

        }


        search.addEventListener(
            'input',
            filterBooks
        );


        filter.addEventListener(
            'change',
            filterBooks
        );


    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>