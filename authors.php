<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<main>

    <!-- =========================================
         AUTHORS HERO
    ========================================== -->

    <section class="page-hero authors-page-hero">

        <div class="container">

            <span class="section-tag">
                SHTËPIA BOTUESE "JAKUP CERAJA"
            </span>

            <h1>
                Autorët
            </h1>

            <p>
                Njihuni me autorët që kanë pasuruar
                botimet tona me veprat dhe krijimtarinë e tyre.
            </p>

        </div>

    </section>


    <!-- =========================================
         AUTHORS SECTION
    ========================================== -->

    <section class="authors-page-section">

        <div class="container">


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


            <!-- AUTHORS GRID -->

            <div
                class="authors-grid"
                id="authorsGrid"
            >

                <?php

                /*
                ==========================================
                GET AUTHORS
                ==========================================
                */

                $authors_query = "

                    SELECT

                        id,
                        fullname,
                        photo,
                        biography

                    FROM authors

                    ORDER BY fullname ASC

                ";


                $authors_result = mysqli_query(
                    $conn,
                    $authors_query
                );


                if (
                    $authors_result &&
                    mysqli_num_rows($authors_result) > 0
                ):

                    while (
                        $author =
                        mysqli_fetch_assoc(
                            $authors_result
                        )
                    ):


                        /*
                        ==================================
                        GET BOOK COUNT
                        ==================================
                        */

                        $author_id =
                            (int)$author['id'];


                        $books_count_query = "

                            SELECT COUNT(*) AS total_books

                            FROM books

                            WHERE author_id = $author_id

                        ";


                        $books_count_result =
                            mysqli_query(
                                $conn,
                                $books_count_query
                            );


                        $books_count = 0;


                        if ($books_count_result) {

                            $count_data =
                                mysqli_fetch_assoc(
                                    $books_count_result
                                );

                            $books_count =
                                (int)$count_data['total_books'];

                        }

                ?>


                    <!-- ==================================
                         AUTHOR CARD
                    =================================== -->

                    <article
                        class="author-card"

                        data-name="<?php
                            echo htmlspecialchars(
                                $author['fullname']
                            );
                        ?>"
                    >


                        <!-- PHOTO -->

                        <div class="author-card-photo">


                            <?php if (!empty($author['photo'])): ?>

                                <img
                                    src="assets/uploads/<?php
                                        echo htmlspecialchars(
                                            $author['photo']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $author['fullname']
                                        );
                                    ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="author-placeholder">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                            <?php endif; ?>


                            <!-- OVERLAY -->

                            <div class="author-card-overlay">

                                <a
                                    href="author-details.php?id=<?php
                                        echo $author_id;
                                    ?>"
                                    class="author-view-button"
                                    aria-label="Shiko autorin"
                                >

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>


                        <!-- CONTENT -->

                        <div class="author-card-content">


                            <span class="author-card-label">
                                AUTOR
                            </span>


                            <h2>

                                <?php
                                echo htmlspecialchars(
                                    $author['fullname']
                                );
                                ?>

                            </h2>


                            <?php if (!empty($author['biography'])): ?>

                                <p>

                                    <?php

                                    $bio =
                                        $author['biography'];

                                    echo htmlspecialchars(
                                        mb_substr(
                                            $bio,
                                            0,
                                            130
                                        )
                                    );

                                    if (
                                        mb_strlen($bio) > 130
                                    ) {
                                        echo '...';
                                    }

                                    ?>

                                </p>

                            <?php else: ?>

                                <p>
                                    Autor i Shtëpisë Botuese
                                    "Jakup Ceraja".
                                </p>

                            <?php endif; ?>


                            <!-- BOOK COUNT -->

                            <div class="author-card-footer">

                                <span>

                                    <i class="fa-solid fa-book"></i>

                                    <?php
                                    echo $books_count;
                                    ?>

                                    <?php

                                    echo (
                                        $books_count == 1
                                    )
                                    ? ' libër'
                                    : ' libra';

                                    ?>

                                </span>


                                <a
                                    href="author-details.php?id=<?php
                                        echo $author_id;
                                    ?>"
                                >

                                    Shiko profilin

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>

                    </article>


                <?php

                    endwhile;

                else:

                ?>


                    <!-- EMPTY -->

                    <div class="authors-empty">

                        <div class="authors-empty-icon">

                            <i class="fa-solid fa-user-pen"></i>

                        </div>

                        <h3>
                            Autorët do të shfaqen së shpejti
                        </h3>

                        <p>
                            Aktualisht nuk ka autorë
                            të regjistruar.
                        </p>

                    </div>


                <?php endif; ?>

            </div>


            <!-- NO RESULTS -->

            <div
                class="authors-no-results"
                id="authorsNoResults"
            >

                <i class="fa-solid fa-user-slash"></i>

                <h3>
                    Nuk u gjet asnjë autor
                </h3>

                <p>
                    Provo të kërkosh me një emër tjetër.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================================
         CTA
    ========================================== -->

    <section class="authors-cta">

        <div class="container">

            <div class="authors-cta-inner">

                <div>

                    <span class="section-tag">
                        LETËRSIA SHQIPTARE
                    </span>

                    <h2>
                        Fjalët e autorëve
                        mbeten ndër breza.
                    </h2>

                    <p>
                        Zbuloni krijimtarinë e autorëve
                        tanë dhe veprat e tyre.
                    </p>

                </div>


                <a
                    href="books.php"
                    class="btn btn-primary"
                >

                    Shfleto Librat

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>


<script src="assets/js/script.js"></script>

</body>
</html>