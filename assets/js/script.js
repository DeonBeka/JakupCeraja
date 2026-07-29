
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