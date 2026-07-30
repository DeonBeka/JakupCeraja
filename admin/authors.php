<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET AUTHORS
// ==========================================

$sql = "
    SELECT
        id,
        fullname,
        photo,
        biography,
        created_at
    FROM authors
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}


$authors = [];

while ($author = mysqli_fetch_assoc($result)) {
    $authors[] = $author;
}


$total_authors = count($authors);

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
        Autorët | Jakup Ceraja Admin
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


            <a
                href="authors.php"
                class="active"
            >

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
                    Autorët
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
                    href="../authors.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Shiko autorët

                </a>


            </div>


        </header>



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <!-- WELCOME -->

            <div class="admin-welcome">


                <div>

                    <span>
                        MENAXHIMI I AUTORËVE
                    </span>

                    <h2>
                        Autorët
                    </h2>

                    <p>
                        Shto, ndrysho dhe menaxho autorët
                        e Shtëpisë Botuese "Jakup Ceraja".
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-pen-nib"></i>

                </div>


            </div>



            <!-- ==================================
                 STATS
            =================================== -->

            <div class="authors-stats">


                <div class="authors-stat-card">

                    <div class="authors-stat-icon">

                        <i class="fa-solid fa-users"></i>

                    </div>


                    <div>

                        <span>
                            Gjithsej autorë
                        </span>

                        <strong>
                            <?php
                            echo $total_authors;
                            ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- ==================================
                 AUTHORS PANEL
            =================================== -->

            <section class="admin-panel-card authors-panel">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            AUTORËT
                        </span>

                        <h3>
                            Lista e autorëve
                        </h3>

                    </div>


                    <a
                        href="add-author.php"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Shto autor

                    </a>


                </div>



                <!-- SEARCH -->

                <div class="authors-toolbar">


                    <div class="authors-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="authorSearch"
                            placeholder="Kërko autor..."
                            autocomplete="off"
                        >

                    </div>


                </div>



                <!-- AUTHORS TABLE -->

                <div class="authors-table-wrapper">


                    <table class="authors-table">


                        <thead>

                            <tr>

                                <th>
                                    Autori
                                </th>

                                <th>
                                    Biografia
                                </th>

                                <th>
                                    Data
                                </th>

                                <th>
                                    Veprime
                                </th>

                            </tr>

                        </thead>


                        <tbody id="authorsTableBody">


                        <?php if (empty($authors)): ?>


                            <tr>

                                <td
                                    colspan="4"
                                    class="authors-empty"
                                >

                                    <i class="fa-solid fa-user-pen"></i>

                                    <strong>
                                        Nuk ka autorë ende
                                    </strong>

                                    <span>
                                        Shto autorin e parë
                                        nga butoni "Shto autor".
                                    </span>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($authors as $author): ?>


                                <tr
                                    data-name="<?php
                                        echo htmlspecialchars(
                                            strtolower(
                                                $author['fullname']
                                            )
                                        );
                                    ?>"
                                >


                                    <!-- AUTHOR -->

                                    <td>


                                        <div class="author-table-info">


                                            <?php if (
                                                !empty($author['photo'])
                                            ): ?>


                                                <img
                                                    src="../assets/uploads/authors/<?php
                                                        echo htmlspecialchars(
                                                            $author['photo']
                                                        );
                                                    ?>"
                                                    alt="<?php
                                                        echo htmlspecialchars(
                                                            $author['fullname']
                                                        );
                                                    ?>"
                                                    class="author-photo-small"
                                                >


                                            <?php else: ?>


                                                <div class="author-photo-placeholder">

                                                    <i class="fa-solid fa-user"></i>

                                                </div>


                                            <?php endif; ?>


                                            <div>


                                                <strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $author['fullname']
                                                    );
                                                    ?>

                                                </strong>


                                                <span>

                                                    ID #<?php
                                                    echo (int)$author['id'];
                                                    ?>

                                                </span>


                                            </div>


                                        </div>


                                    </td>



                                    <!-- BIOGRAPHY -->

                                    <td>


                                        <?php

                                        if (
                                            !empty(
                                                $author['biography']
                                            )
                                        ) {

                                            $bio =
                                                strip_tags(
                                                    $author['biography']
                                                );


                                            echo htmlspecialchars(
                                                mb_substr(
                                                    $bio,
                                                    0,
                                                    100
                                                )
                                            );


                                            if (
                                                mb_strlen(
                                                    $bio
                                                ) > 100
                                            ) {

                                                echo '...';

                                            }

                                        } else {

                                            echo '<span class="author-no-bio">
                                                Pa biografi
                                            </span>';

                                        }

                                        ?>


                                    </td>



                                    <!-- DATE -->

                                    <td>


                                        <?php

                                        if (
                                            !empty(
                                                $author['created_at']
                                            )
                                        ) {

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $author['created_at']
                                                )
                                            );

                                        } else {

                                            echo '-';

                                        }

                                        ?>


                                    </td>



                                    <!-- ACTIONS -->

                                    <td>


                                        <div class="author-actions">


                                            <a
                                                href="edit-author.php?id=<?php
                                                    echo (int)$author['id'];
                                                ?>"
                                                class="author-action edit"
                                                title="Ndrysho autorin"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                            </a>


                                            <a
                                                href="delete-author.php?id=<?php
                                                    echo (int)$author['id'];
                                                ?>"
                                                class="author-action delete"
                                                title="Fshi autorin"
                                                onclick="
                                                    return confirm(
                                                        'A jeni i sigurt që dëshironi ta fshini këtë autor?'
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
            document.getElementById(
                'authorSearch'
            );


        const rows =
            document.querySelectorAll(
                '#authorsTableBody tr[data-name]'
            );


        if (!search) {
            return;
        }


        search.addEventListener(
            'input',
            function () {


                const value =
                    search.value
                        .toLowerCase()
                        .trim();


                rows.forEach(
                    function (row) {


                        const name =
                            row.dataset.name
                            || '';


                        if (
                            name.includes(value)
                        ) {

                            row.style.display = '';

                        } else {

                            row.style.display = 'none';

                        }

                    }
                );

            }
        );


    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>