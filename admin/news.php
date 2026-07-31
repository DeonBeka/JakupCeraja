<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET NEWS
// ==========================================

$sql = "
    SELECT
        id,
        title,
        slug,
        image,
        excerpt,
        category,
        published_at,
        featured,
        created_at
    FROM news
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}


$news = [];
$total_news = 0;
$featured_news = 0;

while ($item = mysqli_fetch_assoc($result)) {

    $news[] = $item;

    $total_news++;

    if ((int)$item['featured'] === 1) {
        $featured_news++;
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
        Lajmet | Jakup Ceraja Admin
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


            <a href="categories.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>Kategori</span>

            </a>


            <a
                href="news.php"
                class="active"
            >

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
                    Lajmet
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
                    href="../news.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Shiko lajmet

                </a>

            </div>

        </header>



        <!-- CONTENT -->

        <div class="admin-content">


            <!-- HEADER -->

            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I LAJMEVE
                    </span>

                    <h2>
                        Lajmet
                    </h2>

                    <p>
                        Shto, ndrysho dhe menaxho lajmet
                        e Shtëpisë Botuese "Jakup Ceraja".
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-newspaper"></i>

                </div>

            </div>



            <!-- STATS -->

            <div class="books-stats">


                <div class="books-stat-card">

                    <div class="books-stat-icon">

                        <i class="fa-solid fa-newspaper"></i>

                    </div>

                    <div>

                        <span>
                            Gjithsej lajme
                        </span>

                        <strong>
                            <?php echo $total_news; ?>
                        </strong>

                    </div>

                </div>


                <div class="books-stat-card">

                    <div class="books-stat-icon">

                        <i class="fa-solid fa-star"></i>

                    </div>

                    <div>

                        <span>
                            Lajme të veçuara
                        </span>

                        <strong>
                            <?php echo $featured_news; ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- NEWS PANEL -->

            <section class="admin-panel-card news-panel">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            LAJMET
                        </span>

                        <h3>
                            Lista e lajmeve
                        </h3>

                    </div>


                    <a
                        href="add-news.php"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Shto lajm

                    </a>

                </div>



                <!-- SEARCH -->

                <div class="news-toolbar">

                    <div class="books-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="newsSearch"
                            placeholder="Kërko lajm..."
                        >

                    </div>


                    <select
                        id="newsFilter"
                        class="books-filter"
                    >

                        <option value="all">
                            Të gjitha
                        </option>

                        <option value="featured">
                            Të veçuara
                        </option>

                        <option value="normal">
                            Të zakonshme
                        </option>

                    </select>

                </div>



                <!-- TABLE -->

                <div class="books-table-wrapper">

                    <table class="books-table news-table">

                        <thead>

                            <tr>

                                <th>
                                    Lajmi
                                </th>

                                <th>
                                    Kategoria
                                </th>

                                <th>
                                    Publikuar
                                </th>

                                <th>
                                    Statusi
                                </th>

                                <th>
                                    Veprime
                                </th>

                            </tr>

                        </thead>


                        <tbody id="newsTableBody">


                        <?php if (empty($news)): ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="books-empty"
                                >

                                    <i class="fa-solid fa-newspaper"></i>

                                    <strong>
                                        Nuk ka lajme ende
                                    </strong>

                                    <span>
                                        Shto lajmin e parë nga butoni
                                        "Shto lajm".
                                    </span>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($news as $item): ?>

                                <tr
                                    data-title="<?php
                                        echo htmlspecialchars(
                                            strtolower(
                                                $item['title']
                                            )
                                        );
                                    ?>"
                                    data-status="<?php
                                        echo (
                                            (int)$item['featured'] === 1
                                            ? 'featured'
                                            : 'normal'
                                        );
                                    ?>"
                                >


                                    <!-- NEWS -->

                                    <td>

                                        <div class="book-table-info">


                                            <?php if (!empty($item['image'])): ?>

                                                <img
                                                src="../assets/uploads/news/<?php
                                                    echo htmlspecialchars($item['image']);
                                                ?>"
                                                alt="<?php
                                                    echo htmlspecialchars($item['title']);
                                                ?>"
                                                class="book-cover-small"
                                            >

                                            <?php endif; ?>


                                            <div>

                                                <strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['title']
                                                    );
                                                    ?>

                                                </strong>

                                                <span>

                                                    ID #<?php
                                                    echo (int)$item['id'];
                                                    ?>

                                                </span>

                                            </div>

                                        </div>

                                    </td>



                                    <!-- CATEGORY -->

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $item['category']
                                            ?: 'Pa kategori'
                                        );
                                        ?>

                                    </td>



                                    <!-- DATE -->

                                    <td>

                                        <?php

                                        if (
                                            !empty(
                                                $item['published_at']
                                            )
                                        ) {

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $item['published_at']
                                                )
                                            );

                                        } else {

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $item['created_at']
                                                )
                                            );

                                        }

                                        ?>

                                    </td>



                                    <!-- STATUS -->

                                    <td>

                                        <?php if (
                                            (int)$item['featured'] === 1
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
                                                href="edit-news.php?id=<?php
                                                    echo (int)$item['id'];
                                                ?>"
                                                class="book-action edit"
                                                title="Ndrysho"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                            </a>


                                            <a
                                                href="delete-news.php?id=<?php
                                                    echo (int)$item['id'];
                                                ?>"
                                                class="book-action delete"
                                                title="Fshi"
                                                onclick="
                                                    return confirm(
                                                        'A jeni i sigurt që dëshironi ta fshini këtë lajm?'
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



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const search =
            document.getElementById('newsSearch');

        const filter =
            document.getElementById('newsFilter');

        const rows =
            document.querySelectorAll(
                '#newsTableBody tr[data-title]'
            );


        function filterNews() {

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


                row.style.display =
                    matchesSearch && matchesFilter
                    ? ''
                    : 'none';

            });

        }


        search.addEventListener(
            'input',
            filterNews
        );


        filter.addEventListener(
            'change',
            filterNews
        );

    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>