
document.addEventListener("DOMContentLoaded", function () {

    console.log("Jakup Ceraja JS loaded");

    const slides = document.querySelectorAll(".hero-slide");
    const dots = document.querySelectorAll(".hero-dot");

    const nextButton = document.querySelector(".hero-next");
    const prevButton = document.querySelector(".hero-prev");

    console.log("Slides:", slides.length);
    console.log("Next button:", nextButton);
    console.log("Previous button:", prevButton);

    if (slides.length <= 1) {
        console.log("Nuk ka mjaft slides.");
        return;
    }

    let currentSlide = 0;
    let autoSlide;


    function showSlide(index) {

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }

        currentSlide = index;


        slides.forEach(function (slide) {
            slide.classList.remove("active");
        });


        dots.forEach(function (dot) {
            dot.classList.remove("active");
        });


        slides[currentSlide].classList.add("active");


        if (dots[currentSlide]) {
            dots[currentSlide].classList.add("active");
        }

        console.log("Current slide:", currentSlide);

    }


    function nextSlide() {

        showSlide(currentSlide + 1);

    }


    function previousSlide() {

        showSlide(currentSlide - 1);

    }


    if (nextButton) {

        nextButton.addEventListener("click", function () {

            console.log("NEXT clicked");

            nextSlide();

            resetAutoSlide();

        });

    }


    if (prevButton) {

        prevButton.addEventListener("click", function () {

            console.log("PREVIOUS clicked");

            previousSlide();

            resetAutoSlide();

        });

    }


    dots.forEach(function (dot, index) {

        dot.addEventListener("click", function () {

            showSlide(index);

            resetAutoSlide();

        });

    });


    function startAutoSlide() {

        autoSlide = setInterval(function () {

            nextSlide();

        }, 6000);

    }


    function resetAutoSlide() {

        clearInterval(autoSlide);

        startAutoSlide();

    }


    showSlide(0);

    startAutoSlide();

});

/* ==============================
   GALLERY FILTERS
============================== */

document.addEventListener("DOMContentLoaded", function () {

    const filterButtons =
        document.querySelectorAll(".gallery-filter");

    const galleryItems =
        document.querySelectorAll(".gallery-item");


    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const filter =
                button.getAttribute("data-filter");


            filterButtons.forEach(function (item) {

                item.classList.remove("active");

            });


            button.classList.add("active");


            galleryItems.forEach(function (item) {

                const category =
                    item.getAttribute("data-category");


                if (
                    filter === "all" ||
                    category === filter
                ) {

                    item.classList.remove("hidden");

                    item.classList.add("show");

                } else {

                    item.classList.add("hidden");

                    item.classList.remove("show");

                }

            });

        });

    });

});


/* ==============================
   GALLERY LIGHTBOX
============================== */

document.addEventListener("DOMContentLoaded", function () {

    const lightbox =
        document.getElementById("galleryLightbox");

    const lightboxImage =
        document.getElementById("lightboxImage");

    const lightboxTitle =
        document.getElementById("lightboxTitle");

    const closeButton =
        document.querySelector(".lightbox-close");

    const viewButtons =
        document.querySelectorAll(".gallery-view");


    if (!lightbox) {
        return;
    }


    viewButtons.forEach(function (button) {

        button.addEventListener("click", function (event) {

            event.stopPropagation();


            const image =
                button.getAttribute("data-image");

            const title =
                button.getAttribute("data-title");


            lightboxImage.src = image;

            lightboxImage.alt = title;

            lightboxTitle.textContent = title;


            lightbox.classList.add("active");

            document.body.style.overflow = "hidden";

        });

    });


    function closeLightbox() {

        lightbox.classList.remove("active");

        document.body.style.overflow = "";

        lightboxImage.src = "";

    }


    if (closeButton) {

        closeButton.addEventListener(
            "click",
            closeLightbox
        );

    }


    lightbox.addEventListener(
        "click",
        function (event) {

            if (event.target === lightbox) {

                closeLightbox();

            }

        }
    );


    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                lightbox.classList.contains("active")
            ) {

                closeLightbox();

            }

        }
    );

});

/* =========================================
   BOOK SEARCH & CATEGORY FILTER
========================================= */

document.addEventListener("DOMContentLoaded", function () {

    const bookSearch = document.getElementById("bookSearch");
    const categoryFilter = document.getElementById("categoryFilter");
    const booksGrid = document.getElementById("booksGrid");
    const noResults = document.getElementById("booksNoResults");

    // Nëse nuk jemi në books.php, mos bëjmë asgjë
    if (!bookSearch || !categoryFilter || !booksGrid) {
        return;
    }

    const bookCards = booksGrid.querySelectorAll(".book-card");


    function filterBooks() {

        const searchValue =
            bookSearch.value
                .toLowerCase()
                .trim();

        const categoryValue =
            categoryFilter.value
                .toLowerCase()
                .trim();

        let visibleBooks = 0;


        bookCards.forEach(function (card) {

            const title =
                (
                    card.dataset.title || ""
                ).toLowerCase();

            const author =
                (
                    card.dataset.author || ""
                ).toLowerCase();

            const category =
                (
                    card.dataset.category || ""
                ).toLowerCase();


            // Search
            const matchesSearch =
                title.includes(searchValue) ||
                author.includes(searchValue);


            // Category
            const matchesCategory =
                categoryValue === "all" ||
                category === categoryValue;


            if (
                matchesSearch &&
                matchesCategory
            ) {

                card.style.display = "";

                visibleBooks++;

            } else {

                card.style.display = "none";

            }

        });


        // Nëse nuk ka rezultate
        if (visibleBooks === 0) {

            noResults.style.display = "block";

        } else {

            noResults.style.display = "none";

        }

    }


    // Search
    bookSearch.addEventListener(
        "input",
        filterBooks
    );


    // Category
    categoryFilter.addEventListener(
        "change",
        filterBooks
    );

});

/* =========================================
   AUTHOR SEARCH
========================================= */

document.addEventListener("DOMContentLoaded", function () {

    const authorSearch =
        document.getElementById("authorSearch");

    const authorsGrid =
        document.getElementById("authorsGrid");

    const authorsNoResults =
        document.getElementById("authorsNoResults");


    if (
        !authorSearch ||
        !authorsGrid ||
        !authorsNoResults
    ) {
        return;
    }


    const authorCards =
        authorsGrid.querySelectorAll(".author-card");


    authorSearch.addEventListener(
        "input",
        function () {

            const searchValue =
                authorSearch.value
                    .toLowerCase()
                    .trim();


            let visibleAuthors = 0;


            authorCards.forEach(function (card) {

                const name =
                    (
                        card.dataset.name || ""
                    ).toLowerCase();


                if (
                    name.includes(searchValue)
                ) {

                    card.style.display = "";

                    visibleAuthors++;

                } else {

                    card.style.display = "none";

                }

            });


            if (visibleAuthors === 0) {

                authorsNoResults.style.display =
                    "block";

            } else {

                authorsNoResults.style.display =
                    "none";

            }

        }
    );

});

/* =========================================
   NEWS SEARCH + FILTER
========================================= */

document.addEventListener("DOMContentLoaded", function () {

    const newsSearch =
        document.getElementById("newsSearch");

    const newsGrid =
        document.getElementById("newsGrid");

    const noResults =
        document.getElementById("newsNoResults");

    const filterButtons =
        document.querySelectorAll(
            ".news-filter-btn"
        );


    if (
        !newsSearch ||
        !newsGrid
    ) {
        return;
    }


    const newsCards =
        newsGrid.querySelectorAll(".news-card");


    let selectedCategory = "all";


    function filterNews() {

        const search =
            newsSearch.value
                .toLowerCase()
                .trim();


        let visible = 0;


        newsCards.forEach(function (card) {

            const title =
                (
                    card.dataset.title || ""
                ).toLowerCase();


            const category =
                (
                    card.dataset.category || ""
                ).toLowerCase();


            const categoryFilter =
                selectedCategory.toLowerCase();


            const matchesSearch =
                title.includes(search);


            const matchesCategory =
                categoryFilter === "all" ||
                category === categoryFilter;


            if (
                matchesSearch &&
                matchesCategory
            ) {

                card.style.display = "";

                visible++;

            } else {

                card.style.display = "none";

            }

        });


        if (visible === 0) {

            noResults.style.display =
                "block";

        } else {

            noResults.style.display =
                "none";

        }

    }


    newsSearch.addEventListener(
        "input",
        filterNews
    );


    filterButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                filterButtons.forEach(
                    function (btn) {

                        btn.classList.remove(
                            "active"
                        );

                    }
                );


                button.classList.add(
                    "active"
                );


                selectedCategory =
                    button.dataset.category ||
                    "all";


                filterNews();

            }
        );

    });

});

/* ==========================
   MOBILE MENU
========================== */

document.addEventListener("DOMContentLoaded", function () {

    const menuBtn = document.querySelector(".menu-btn");
    const navbar = document.querySelector(".navbar");

    if (!menuBtn || !navbar) {
        return;
    }

    menuBtn.addEventListener("click", function () {

        navbar.classList.toggle("menu-open");

        const icon = menuBtn.querySelector("i");

        if (navbar.classList.contains("menu-open")) {

            icon.classList.remove("fa-bars");
            icon.classList.add("fa-xmark");

        } else {

            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");

        }

    });


    /* CLOSE MENU WHEN LINK IS CLICKED */

    const navLinks =
        navbar.querySelectorAll(".nav-links a");

    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            navbar.classList.remove("menu-open");

            const icon = menuBtn.querySelector("i");

            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");

        });

    });

});