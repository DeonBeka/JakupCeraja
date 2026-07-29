<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<!DOCTYPE html>
<html lang="sq">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Galeria | Shtëpia Botuese "Jakup Ceraja"</title>

    <meta
        name="description"
        content="Galeria e Shtëpisë Botuese Jakup Ceraja."
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/responsive.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>





<main>

    <section class="page-hero">

        <div class="container">

            <span class="section-tag">
                SHTËPIA BOTUESE "JAKUP CERAJA"
            </span>

            <h1>
                Galeria
            </h1>

            <p>
                Momente nga aktivitetet, promovimet,
                panairet dhe ngjarjet tona letrare.
            </p>

        </div>

    </section>


    <section class="gallery-page">

        <div class="container">

            <div class="gallery-grid">

                <?php

                $gallery_query = "
                    SELECT id, image, title, created_at
                    FROM gallery
                    ORDER BY id DESC
                ";

                $gallery_result = mysqli_query(
                    $conn,
                    $gallery_query
                );


                if (
                    $gallery_result &&
                    mysqli_num_rows($gallery_result) > 0
                ):

                    while (
                        $gallery_item =
                        mysqli_fetch_assoc($gallery_result)
                    ):

                ?>

                    <article class="gallery-item">

                        <img
                            src="assets/uploads/gallery/<?php echo htmlspecialchars($gallery_item['image']); ?>"
                            alt="<?php echo htmlspecialchars($gallery_item['title']); ?>"
                            loading="lazy"
                        >


                        <div class="gallery-overlay">

                            <div class="gallery-info">

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $gallery_item['title']
                                    );
                                    ?>
                                </h3>

                            </div>


                            <button
                                type="button"
                                class="gallery-view"
                                data-image="assets/uploads/gallery/<?php echo htmlspecialchars($gallery_item['image']); ?>"
                                data-title="<?php echo htmlspecialchars($gallery_item['title']); ?>"
                            >

                                <i class="fa-solid fa-expand"></i>

                            </button>

                        </div>

                    </article>

                <?php

                    endwhile;

                else:

                ?>

                    <p class="no-books">
                        Aktualisht nuk ka fotografi në galeri.
                    </p>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>


<!-- LIGHTBOX -->

<div
    class="gallery-lightbox"
    id="galleryLightbox"
>

    <button
        type="button"
        class="lightbox-close"
    >

        <i class="fa-solid fa-xmark"></i>

    </button>


    <div class="lightbox-content">

        <img
            src=""
            alt=""
            id="lightboxImage"
        >

        <h3 id="lightboxTitle"></h3>

    </div>

</div>


<?php include 'includes/footer.php'; ?>


<script src="assets/js/script.js"></script>

</body>

</html>