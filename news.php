<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<main>

    <!-- =========================================
         NEWS HERO
    ========================================== -->

    <section class="page-hero news-page-hero">

        <div class="container">

            <span class="section-tag">
                SHTËPIA BOTUESE "JAKUP CERAJA"
            </span>

            <h1>
                Lajmet
            </h1>

            <p>
                Ndiqni botimet e reja, aktivitetet,
                eventet dhe lajmet nga bota e librit.
            </p>

        </div>

    </section>


    <!-- =========================================
         NEWS
    ========================================== -->

    <section class="news-page-section">

        <div class="container">


            <!-- SEARCH + FILTER -->

            <div class="news-toolbar">

                <div class="news-search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        id="newsSearch"
                        placeholder="Kërko në lajme..."
                        autocomplete="off"
                    >

                </div>


                <div class="news-filter">

                    <button
                        class="news-filter-btn active"
                        data-category="all"
                    >
                        Të gjitha
                    </button>


                    <?php

                    $categories_query = "

                        SELECT DISTINCT category

                        FROM news

                        WHERE category IS NOT NULL
                        AND category != ''

                        ORDER BY category ASC

                    ";

                    $categories_result =
                        mysqli_query(
                            $conn,
                            $categories_query
                        );


                    if ($categories_result):

                        while (
                            $category =
                            mysqli_fetch_assoc(
                                $categories_result
                            )
                        ):

                    ?>

                        <button
                            class="news-filter-btn"
                            data-category="<?php
                                echo htmlspecialchars(
                                    $category['category']
                                );
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $category['category']
                            );
                            ?>

                        </button>

                    <?php

                        endwhile;

                    endif;

                    ?>

                </div>

            </div>


            <!-- NEWS GRID -->

            <div
                class="news-grid"
                id="newsGrid"
            >

                <?php

                $news_query = "

                    SELECT

                        id,
                        title,
                        slug,
                        image,
                        excerpt,
                        category,
                        published_at

                    FROM news

                    ORDER BY
                        published_at DESC,
                        id DESC

                ";


                $news_result = mysqli_query(
                    $conn,
                    $news_query
                );


                if (
                    $news_result &&
                    mysqli_num_rows($news_result) > 0
                ):

                    while (
                        $article =
                        mysqli_fetch_assoc(
                            $news_result
                        )
                    ):

                ?>

                    <article
                        class="news-card"

                        data-category="<?php
                            echo htmlspecialchars(
                                $article['category']
                            );
                        ?>"

                        data-title="<?php
                            echo htmlspecialchars(
                                $article['title']
                            );
                        ?>"
                    >


                        <!-- IMAGE -->

                        <div class="news-card-image">

                            <?php if (!empty($article['image'])): ?>

                                <img
                                    src="assets/uploads/<?php
                                        echo htmlspecialchars(
                                            $article['image']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $article['title']
                                        );
                                    ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="news-image-placeholder">

                                    <i class="fa-solid fa-newspaper"></i>

                                </div>

                            <?php endif; ?>


                            <?php if (!empty($article['category'])): ?>

                                <span class="news-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $article['category']
                                    );
                                    ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- CONTENT -->

                        <div class="news-card-content">


                            <div class="news-date">

                                <i class="fa-regular fa-calendar"></i>

                                <?php

                                if (
                                    !empty(
                                        $article['published_at']
                                    )
                                ) {

                                    echo date(
                                        'd.m.Y',
                                        strtotime(
                                            $article['published_at']
                                        )
                                    );

                                }

                                ?>

                            </div>


                            <h2>

                                <?php
                                echo htmlspecialchars(
                                    $article['title']
                                );
                                ?>

                            </h2>


                            <p>

                                <?php

                                if (
                                    !empty(
                                        $article['excerpt']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $article['excerpt']
                                    );

                                } else {

                                    echo 'Lexoni më shumë
                                    rreth këtij lajmi.';

                                }

                                ?>

                            </p>


                            <a
                                href="news-details.php?id=<?php
                                    echo (int)$article['id'];
                                ?>"
                            >

                                Lexo më shumë

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </div>

                    </article>


                <?php

                    endwhile;

                else:

                ?>

                    <div class="news-empty">

                        <i class="fa-solid fa-newspaper"></i>

                        <h3>
                            Nuk ka lajme ende
                        </h3>

                        <p>
                            Lajmet do të shfaqen këtu
                            sapo të publikohen.
                        </p>

                    </div>

                <?php endif; ?>

            </div>


            <!-- NO RESULTS -->

            <div
                class="news-no-results"
                id="newsNoResults"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <h3>
                    Nuk u gjet asnjë lajm
                </h3>

                <p>
                    Provo një kërkim ose kategori tjetër.
                </p>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>



</body>
</html>