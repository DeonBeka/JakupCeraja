<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';


// =========================================
// GET NEWS ID
// =========================================

$news_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;


// =========================================
// GET ARTICLE
// =========================================

$news_query = "

    SELECT
        id,
        title,
        image,
        content,
        created_at,
        slug,
        excerpt,
        category,
        published_at,
        featured

    FROM news

    WHERE id = $news_id

    LIMIT 1

";

$news_result = mysqli_query(
    $conn,
    $news_query
);


// =========================================
// ARTICLE NOT FOUND
// =========================================

if (
    !$news_result ||
    mysqli_num_rows($news_result) === 0
) {

    http_response_code(404);

    ?>

    <main>

        <section class="news-not-found">

            <div class="container">

                <div class="news-not-found-inner">

                    <i class="fa-solid fa-newspaper"></i>

                    <h1>
                        Lajmi nuk u gjet
                    </h1>

                    <p>
                        Lajmi që po kërkoni nuk ekziston
                        ose nuk është më i disponueshëm.
                    </p>

                    <a
                        href="news.php"
                        class="btn btn-primary"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Kthehu te Lajmet

                    </a>

                </div>

            </div>

        </section>

    </main>

    <?php

    include 'includes/footer.php';

    exit;
}


$article = mysqli_fetch_assoc(
    $news_result
);

?>

<main>


    <!-- =========================================
         NEWS ARTICLE
    ========================================== -->

    <article class="news-details-page">


        <!-- =====================================
             HERO
        ====================================== -->

        <section class="news-details-hero">

            <div class="container">

                <!-- BREADCRUMB -->

                <div class="news-details-breadcrumb">

                    <a href="index.php">
                        Ballina
                    </a>

                    <i class="fa-solid fa-chevron-right"></i>

                    <a href="news.php">
                        Lajmet
                    </a>

                    <i class="fa-solid fa-chevron-right"></i>

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $article['title']
                        );
                        ?>

                    </span>

                </div>


                <!-- CATEGORY -->

                <?php if (!empty($article['category'])): ?>

                    <span class="section-tag">

                        <?php
                        echo htmlspecialchars(
                            $article['category']
                        );
                        ?>

                    </span>

                <?php endif; ?>


                <!-- TITLE -->

                <h1>

                    <?php
                    echo htmlspecialchars(
                        $article['title']
                    );
                    ?>

                </h1>


                <!-- DATE -->

                <div class="news-details-meta">

                    <span>

                        <i class="fa-regular fa-calendar"></i>

                        <?php

                        $date =
                            !empty(
                                $article['published_at']
                            )
                            ? $article['published_at']
                            : $article['created_at'];

                        echo date(
                            'd.m.Y',
                            strtotime($date)
                        );

                        ?>

                    </span>

                    <span>
                        <i class="fa-solid fa-book-open"></i>
                        Shtëpia Botuese "Jakup Ceraja"
                    </span>

                </div>

            </div>

        </section>


        <!-- =====================================
             ARTICLE CONTENT
        ====================================== -->

        <section class="news-details-content-section">

            <div class="container">

                <div class="news-details-layout">


                    <!-- MAIN CONTENT -->

                    <div class="news-details-main">


                        <!-- IMAGE -->

                        <?php if (!empty($article['image'])): ?>

                            <div class="news-details-image">

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
                                >

                            </div>

                        <?php endif; ?>


                        <!-- EXCERPT -->

                        <?php if (!empty($article['excerpt'])): ?>

                            <div class="news-details-excerpt">

                                <?php
                                echo htmlspecialchars(
                                    $article['excerpt']
                                );
                                ?>

                            </div>

                        <?php endif; ?>


                        <!-- CONTENT -->

                        <div class="news-details-text">

                            <?php

                            /*
                             * nl2br() ruan rreshtat e rinj
                             * nga databaza.
                             *
                             * htmlspecialchars() mbron
                             * përmbajtjen nga HTML i padëshiruar.
                             */

                            echo nl2br(
                                htmlspecialchars(
                                    $article['content']
                                )
                            );

                            ?>

                        </div>


                        <!-- BACK BUTTON -->

                        <div class="news-details-back">

                            <a
                                href="news.php"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-arrow-left"></i>

                                Të gjitha lajmet

                            </a>

                        </div>

                    </div>


                    <!-- SIDEBAR -->

                    <aside class="news-details-sidebar">


                        <!-- CATEGORY -->

                        <div class="news-sidebar-card">

                            <span>
                                KATEGORIA
                            </span>

                            <h3>

                                <?php

                                if (
                                    !empty(
                                        $article['category']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $article['category']
                                    );

                                } else {

                                    echo 'Lajme';

                                }

                                ?>

                            </h3>

                        </div>


                        <!-- DATE -->

                        <div class="news-sidebar-card">

                            <span>
                                PUBLIKUAR
                            </span>

                            <h3>

                                <?php

                                echo date(
                                    'd.m.Y',
                                    strtotime($date)
                                );

                                ?>

                            </h3>

                        </div>


                        <!-- SHARE -->

                        <div class="news-sidebar-card">

                            <span>
                                SHPËRNDAJE
                            </span>

                            <div class="news-share">

                                <a
                                    href="https://www.facebook.com/sharer/sharer.php?u=<?php
                                        echo urlencode(
                                            'http://' .
                                            $_SERVER['HTTP_HOST'] .
                                            $_SERVER['REQUEST_URI']
                                        );
                                    ?>"
                                    target="_blank"
                                    rel="noopener"
                                    aria-label="Facebook"
                                >

                                    <i class="fa-brands fa-facebook-f"></i>

                                </a>


                                <a
                                    href="https://www.linkedin.com/sharing/share-offsite/?url=<?php
                                        echo urlencode(
                                            'http://' .
                                            $_SERVER['HTTP_HOST'] .
                                            $_SERVER['REQUEST_URI']
                                        );
                                    ?>"
                                    target="_blank"
                                    rel="noopener"
                                    aria-label="LinkedIn"
                                >

                                    <i class="fa-brands fa-linkedin-in"></i>

                                </a>

                            </div>

                        </div>


                    </aside>

                </div>

            </div>

        </section>


    </article>

</main>


<?php include 'includes/footer.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>